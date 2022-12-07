<?php

namespace Tests\Feature\Api;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Regions\Province;
use App\Entities\Services\Rate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ApiRegionsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function retrieve_provinces_list()
    {
        $user = $this->loginAsUser();
        $provinces = Province::pluck('name','id');

        $this->getJson(route('api.regions.provinces'), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeJson($provinces->toArray());
    }

    /** @test */
    public function retrieve_all_cities_list()
    {
        $user = $this->loginAsUser();
        $cities = City::pluck('name','id');

        $this->getJson(route('api.regions.cities'), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeJson($cities->toArray());
    }

    /** @test */
    public function retrieve_cities_list()
    {
        $user = $this->loginAsUser();
        $provinceId = Province::all()->random()->id;
        $cities = City::whereProvinceId($provinceId)->pluck('name','id');

        $this->getJson(route('api.regions.cities', ['province_id' => $provinceId]), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeJson($cities->toArray());
    }

    /** @test */
    public function retrieve_districts_list()
    {
        $user = $this->loginAsUser();
        $cityId = City::all()->random()->id;
        $districts = District::whereCityId($cityId)->pluck('name','id');

        $this->getJson(route('api.regions.districts', ['city_id' => $cityId]), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeJson($districts->toArray());
    }

    /** @test */
    public function retrieve_destination_districts_list()
    {
        $user = $this->loginAsUser();

        $rate1 = factory(Rate::class, 'city_to_district')->create([
            'orig_city_id' => 6371, 'dest_city_id' => 6372, 'dest_district_id' => 6372010
        ]);
        $rate2 = factory(Rate::class, 'city_to_district')->create([
            'orig_city_id' => 6371, 'dest_city_id' => 6372, 'dest_district_id' => 6372020
        ]);
        $rate3 = factory(Rate::class, 'city_to_district')->create([
            'orig_city_id' => 6371, 'dest_city_id' => 6303, 'dest_district_id' => 6303010
        ]);

        $city = City::findOrFail(6371);
        $this->assertEquals(3, $city->destinationDistricts->count());

        $this->getJson(route('api.regions.destination-districts', ['dest_city_id' => $rate1->dest_city_id, 'orig_city_id'=> $rate1->orig_city_id]), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeJsonEquals([
            6372010 => 'Kec. Landasan Ulin',
            6372020 => 'Kec. Cempaka',
        ]);
    }
}
