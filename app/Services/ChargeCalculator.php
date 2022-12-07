<?php

namespace App\Services;

use App\Entities\Regions\City;
use App\Entities\Services\Rate;
use App\Entities\Services\Service;

/**
 * Charge Calculator Class
 */
class ChargeCalculator
{
    public $rate;
    public $receiptQuery;
    public $originId;
    public $destinationId;
    public $rateState;
    public $charge;
    public $baseRate;

    public function calculateByReceiptQuery(array $receiptQuery)
    {
        $this->receiptQuery = $receiptQuery;
        $this->originId = $this->setOriginId();
        $this->destinationId = $this->setDestinationId();
        $this->rateState = $this->setCorrectRateState();
        $this->charged_on = $this->receiptQuery['charged_on'] ?? 1;
        $this->discount = $this->receiptQuery['discount'] ?? 0;
        $this->packing_cost = $this->receiptQuery['packing_cost'] ?? 0;
        $this->add_cost = $this->receiptQuery['add_cost'] ?? 0;

        $this->setBaseChargeAndRate();

        return $this;
    }

    public function getCharge()
    {
        $charge = $this->charge;
        if ($charge == 0) {
            $this->discount = 0;
            $this->packing_cost = 0;
            return 0;
        }
        $charge = $charge - $this->discount;
        $charge = $charge + $this->packing_cost;
        $charge = $charge + $this->getAdminFee();
        return $charge;
    }

    public function setBaseChargeAndRate()
    {
        $customerId = $this->receiptQuery['customer_id'] ?: 0;
        $origDistrictId = $this->receiptQuery['orig_district_id'] ?: '0';
        $destDistrictId = $this->receiptQuery['dest_district_id'] ?: '0';

        $rates = Rate::where([
            'orig_city_id'     => $this->receiptQuery['orig_city_id'],
            'orig_district_id' => $origDistrictId,
            'dest_city_id'     => $this->receiptQuery['dest_city_id'],
            'dest_district_id' => $destDistrictId,
            'service_id'       => $this->receiptQuery['service_id'],
        ])->get();

        $this->rate = null;

        if ($customerId) {
            $this->rate = $rates->where('customer_id', $customerId)->first();
            if (is_null($this->rate)) {
                $this->rate = $rates->where('customer_id', 0)->first();
            }
        } else {
            $this->rate = $rates->where('customer_id', 0)->first();
        }

        if ($this->charged_on == 1) {
            $this->charge = $this->rate ? ($this->getChargedWeight() * $this->rate->rate_kg) : 0;
            $this->baseRate = $this->rate ? $this->rate->rate_kg : 0;
        } else {
            $this->charge = $this->rate ? ($this->receiptQuery['pcs_count'] * $this->rate->rate_pc) : 0;
            $this->baseRate = $this->rate ? $this->rate->rate_pc : 0;
        }
    }

    private function getChargedWeight()
    {
        if ($this->hasBaseRate() == false) {
            return $this->receiptQuery['charged_weight'];
        }

        return ($this->rate->min_weight <= $this->receiptQuery['charged_weight']) ? $this->receiptQuery['charged_weight'] : $this->rate->min_weight;
    }

    private function setOriginId()
    {
        if ($this->receiptQuery['orig_district_id'] != 0) {
            return $this->receiptQuery['orig_district_id'];
        }

        return $this->receiptQuery['orig_city_id'];
    }

    private function setDestinationId()
    {
        if ($this->receiptQuery['dest_district_id'] != 0) {
            return $this->receiptQuery['dest_district_id'];
        }

        return $this->receiptQuery['dest_city_id'];
    }

    private function setCorrectRateState()
    {
        $originIdLen = strlen($this->originId);
        $destinationIdLen = strlen($this->destinationId);

        $rateState = 'city_to_city';

        if ($originIdLen == 4 && $destinationIdLen == 7) {
            $rateState = 'city_to_district';
        }

        if ($originIdLen == 7 && $destinationIdLen == 4) {
            $rateState = 'district_to_city';
        }

        if ($originIdLen == 7 && $destinationIdLen == 7) {
            $rateState = 'district_to_district';
        }

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

    public function getService()
    {
        return Service::getNameById($this->receiptQuery['service_id']);
    }

    public function toArray()
    {
        $data = [
            'customer_id'      => $this->receiptQuery['customer_id'] ?? null,
            'orig_city_id'     => $this->receiptQuery['orig_city_id'],
            'orig_district_id' => $this->receiptQuery['orig_district_id'] ?: '0',
            'dest_city_id'     => $this->receiptQuery['dest_city_id'],
            'dest_district_id' => $this->receiptQuery['dest_district_id'] ?: '0',
            'service_id'       => $this->receiptQuery['service_id'],
            'pcs_count'        => $this->receiptQuery['pcs_count'] ?? 1,
            'items_count'      => $this->receiptQuery['items_count'] ?? 1,
            'charged_weight'   => $this->receiptQuery['charged_weight'] ?? 1,
            'charged_on'       => $this->receiptQuery['charged_on'] ?? 1, // 1: weight, 2:item
            'pack_type_id'     => $this->receiptQuery['pack_type_id'] ?? $this->rate->pack_type_id,
            'package_value'    => $this->receiptQuery['package_value'] ?? '',
            'be_insured'       => $this->receiptQuery['be_insured'] ?? 0,
            'discount'         => $this->receiptQuery['discount'] ?? 0,
            'packing_cost'     => $this->receiptQuery['packing_cost'] ?? 0,
            'add_cost'         => $this->receiptQuery['add_cost'] ?? 0,
        ];

        $data['insurance_cost'] = $this->getInsuraceCost();
        $data['admin_fee'] = $this->getAdminFee();
        if ($this->hasBaseRate()) {
            $data['rate_id'] = $this->rate->id;
            $data['base_rate'] = $this->baseRate;
            $data['base_charge'] = $this->charge;
            $data['subtotal'] = $this->charge - $this->discount;
            $data['total'] = $data['subtotal'] + $this->packing_cost + $this->add_cost + $data['admin_fee'] + $this->getInsuraceCost();
        } else {
            $data['rate_id'] = null;
            $data['base_rate'] = null;
            $data['base_charge'] = null;
            $data['subtotal'] = null;
            $data['total'] = null;
        }

        return $data;
    }

    private function getInsuraceCost()
    {
        return $this->isInsurable() ? 0.003 * $this->receiptQuery['package_value'] : 0;
    }

    private function getAdminFee()
    {
        return isset($this->receiptQuery['admin_fee']) && $this->receiptQuery['admin_fee'] == 1 ? 2000 : 0;
    }

    private function isInsurable()
    {
        if (!isset($this->receiptQuery['be_insured'])) {
            return false;
        }

        return $this->receiptQuery['be_insured'] && $this->receiptQuery['package_value'] >= 500000;
    }

    public function hasBaseRate()
    {
        return !!$this->rate;
    }

    public function isMinChargeApplied()
    {
        return $this->rate->min_weight > $this->receiptQuery['charged_weight'] && $this->charged_on == 1;
    }
}
