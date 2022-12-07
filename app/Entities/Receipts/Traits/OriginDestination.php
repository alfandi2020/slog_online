<?php

namespace App\Entities\Receipts\Traits;

use App\Entities\Regions\City;
use App\Entities\Regions\District;

trait OriginDestination {

    public function origin()
    {
        if ($this->orig_district_id)
            return $this->belongsTo(District::class, 'orig_district_id');

        return $this->belongsTo(City::class, 'orig_city_id');
    }

    public function originName()
    {
        return $this->origin->name;
    }

    public function origCity()
    {
        return $this->belongsTo(City::class, 'orig_city_id');
    }

    public function origCityName()
    {
        return $this->origCity->name;
    }

    public function origDistrict()
    {
        return $this->belongsTo(District::class, 'orig_district_id');
    }

    public function origDistrictName()
    {
        return $this->orig_district_id ? $this->origDistrict->name : null;
    }

    public function destination()
    {
        if ($this->dest_district_id)
            return $this->belongsTo(District::class, 'dest_district_id');

        return $this->belongsTo(City::class, 'dest_city_id');
    }

    public function destinationName()
    {
        if ($this->destination)
            return $this->destination->name;

        if ($this->rate)
            return $this->rate->destination->name;

        return null;
    }

    public function destCity()
    {
        return $this->belongsTo(City::class, 'dest_city_id');
    }

    public function destCityName()
    {
        return $this->destCity->name;
    }

    public function destDistrict()
    {
        return $this->belongsTo(District::class, 'dest_district_id');
    }

    public function destDistrictName()
    {
        return $this->dest_district_id ? $this->destDistrict->name : null;
    }
}