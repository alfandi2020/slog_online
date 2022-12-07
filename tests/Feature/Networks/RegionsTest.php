<?php

namespace Tests\Feature\Networks;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Regions\Province;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ViewRegionsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function retrieve_provinces_list()
    {
        $user = $this->loginAsAdmin();
        $this->visit(route('admin.regions.provinces'));
    }

    /** @test */
    public function retrieve_cities_list()
    {
        $user = $this->loginAsAdmin();
        $provinceId = Province::all()->random()->id;
        $this->visit(route('admin.regions.cities', ['province_id' => $provinceId]));
    }

    /** @test */
    public function retrieve_districts_list()
    {
        $user = $this->loginAsAdmin();
        $cityId = City::all()->random()->id;
        $this->visit(route('admin.regions.districts', ['city_id' => $cityId]));
    }
}
