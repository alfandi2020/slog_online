<?php

namespace App\Http\Controllers\Api;

use App\Entities\Regions\City;
use App\Entities\Regions\RegionQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    private $queryObject;

    public function __construct(RegionQuery $queryObject)
    {
        $this->queryObject = $queryObject;
    }

    public function provinces()
    {
        return $this->queryObject->getProvincesList();
    }

    public function cities(Request $request)
    {
        if ($provinceId = $request->get('province_id'))
            return $this->queryObject->getCitiesList($provinceId);

        return $this->queryObject->getCitiesList();
    }

    public function districts(Request $request)
    {
        if ($cityId = $request->get('city_id'))
            return $this->queryObject->getDistrictsList($cityId);

        return [];
    }

    public function destinationDistricts(Request $request)
    {
        $destinationDistricts = [];
        if ($request->has('orig_city_id') && $request->has('dest_city_id')) {
            $districts = City::findOrFail($request->get('orig_city_id'))
                ->destinationDistricts()
                ->where('dest_city_id', $request->get('dest_city_id'))
                ->get();

            foreach ($districts as $district)
                $destinationDistricts[$district->id] = $district->name;
        }

        return $destinationDistricts;
    }
}
