<?php

namespace App\Entities\Regions;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Regions\Province;

/**
* Region Query Object
*/
class RegionQuery
{
    public function getProvincesList()
    {
        return Province::pluck('name','id');
    }

    public function getCitiesList($provinceId = null)
    {
        if (!$provinceId)
            return City::pluck('name','id');

        return City::whereProvinceId($provinceId)->pluck('name','id');
    }

    public function getDistrictsList($cityId = null)
    {
        if (!$cityId)
            return [];

        return District::whereCityId($cityId)->pluck('name','id');
    }
}