<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Regions\Province;
use App\Entities\Regions\RegionQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function provinces()
    {
        $provinces = Province::withCount('cities','districts')->get();
        return view('admin.regions.provinces', compact('provinces'));
    }

    public function cities(Request $request, RegionQuery $regionQuery)
    {
        $provinceId = (int) $request->get('province_id');
        $province = Province::find($provinceId);

        if (is_null($province))
            return redirect()->route('admin.regions.provinces');

        $cities = City::whereProvinceId($province->id)->withCount('districts')->get();

        $provinces = $regionQuery->getProvincesList();
        $pageTitle = trans('address.province') . ' ' . $province->id . ' - ' . $province->name;
        return view('admin.regions.cities', compact('pageTitle','provinces','cities'));
    }

    public function districts(Request $request, RegionQuery $regionQuery)
    {
        $cityId = (int) $request->get('city_id');
        $city = City::find($cityId);

        if (is_null($city))
            return redirect()->route('admin.regions.provinces');

        $districts = District::whereCityId($city->id)->get();

        $pageTitle = $city ? trans('address.city') . ' ' . $city->id . ' - ' . $city->name : 'Pilih ' . trans('address.city');
        return view('admin.regions.districts', compact('pageTitle','cities','districts','city'));
    }
}
