<?php

namespace Tests\Unit\Integration;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Regions\Province;
use App\Entities\Regions\RegionQuery;
use Tests\TestCase;

class RegionQueryTest extends TestCase
{
    /** @test */
    public function it_can_fetch_provinces_list()
    {
        $provinces = Province::pluck('name','id');
        $regionQuery = $this->getRegionQueryObject();
        $this->assertEquals($provinces, $regionQuery->getProvincesList());
    }

    /** @test */
    public function it_can_fetch_all_cities_list()
    {
        $cities = City::pluck('name','id');
        $regionQuery = $this->getRegionQueryObject();
        $this->assertEquals($cities, $regionQuery->getCitiesList());
    }

    /** @test */
    public function it_can_fetch_cities_list()
    {
        $provinceId = Province::all()->random()->id;
        $cities = City::whereProvinceId($provinceId)->pluck('name','id');
        $regionQuery = $this->getRegionQueryObject();
        $this->assertEquals($cities, $regionQuery->getCitiesList($provinceId));
    }

    /** @test */
    public function it_can_fetch_districts_list()
    {
        $cityId = City::all()->random()->id;
        $districts = District::whereCityId($cityId)->pluck('name','id');
        $regionQuery = $this->getRegionQueryObject();
        $this->assertEquals($districts, $regionQuery->getDistrictsList($cityId));
    }

    private function getRegionQueryObject()
    {
        return new RegionQuery;
    }
}
