<?php

namespace App\Services;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Services\Rate;

/**
* Charge Calculator Class
*/
class CostsCalculator
{
    public $rates;

    public $originId;
    public $destinationId;
    public $weight;
    public $rateState;

    public function calculate($originId, $destinationId, $weight = 1, $rateState = 'city_to_city')
    {
        $this->originId      = $originId;
        $this->destinationId = $destinationId;
        $this->weight        = $weight;
        $this->rateState     = $this->getCorrectRateState();

        switch ($this->rateState) {
            case 'city_to_district'    : $this->getbaseRates('orig_city_id', 'dest_district_id'); break;
            case 'district_to_district': $this->getbaseRates('orig_district_id', 'dest_district_id'); break;
            case 'district_to_city'    : $this->getbaseRates('orig_district_id', 'dest_city_id'); break;
            default                    : $this->getbaseRates('orig_city_id', 'dest_city_id'); break; // city_to_city
        }

        return $this;
    }

    public function getRates()
    {
        return $this->rates;
    }

    private function getbaseRates($originAttr, $destinationAttr)
    {
        $rates = Rate::where($originAttr, $this->originId)
            ->where($destinationAttr, $this->destinationId)
            ->where('customer_id', 0)
            ->get();

        // asked: district to district; exists: district to city; then applied rate : district to city
        if ($rates->isEmpty() && $this->rateState == 'district_to_district') {
            $destinationId = substr($this->destinationId, 0, 4);

            $rates = Rate::where($originAttr, $this->originId)
                ->where('dest_city_id', $destinationId)
                ->where('customer_id', 0)
                ->get();
        }

        // asked: city to district; exists: city to city; then applied rate : city to city
        if ($rates->isEmpty() && $this->rateState == 'city_to_district') {
            $destinationId = substr($this->destinationId, 0, 4);

            $rates = Rate::where('orig_city_id', $this->originId)
                ->where('dest_city_id', $destinationId)
                ->where('customer_id', 0)
                ->get();
        }

        // asked: district to city; exists: city to city; then applied rate : city to city
        if ($rates->isEmpty()  && $this->rateState == 'district_to_city') {
            $originId = substr($this->originId, 0, 4);

            $rates = Rate::where('orig_city_id', $originId)
                ->where($destinationAttr, $this->destinationId)
                ->where('customer_id', 0)
                ->get();
        }

        if ($rates->isEmpty() == false) {
            $rates = $rates->each(function($rate, $key) {
                $rate->cost = $this->weight * $rate->rate_kg;
            });
        }

        $this->rates = $rates;

        return $this;
    }

    private function getCorrectRateState()
    {
        $originIdLen = strlen($this->originId);
        $destinationIdLen = strlen($this->destinationId);

        $rateState = 'city_to_city';

        if ($originIdLen == 4 && $destinationIdLen == 7)
            $rateState = 'city_to_district';
        if ($originIdLen == 7 && $destinationIdLen == 4)
            $rateState = 'district_to_city';
        if ($originIdLen == 7 && $destinationIdLen == 7)
            $rateState = 'district_to_district';

        return $rateState;
    }

    public function getOrigin()
    {
        $originIdLen = strlen($this->originId);

        if ($originIdLen == 7) {
            $district = District::find($this->originId);
            return $district ? $district->name : null;
        }

        $city = City::find($this->originId);
        return $city ? $city->name : null;
    }

    public function getDestination()
    {
        $destinationIdLen = strlen($this->destinationId);

        if ($destinationIdLen == 7) {
            $district = District::find($this->destinationId);
            return $district ? $district->name : null;
        }

        $city = City::find($this->destinationId);
        return $city ? $city->name : null;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}