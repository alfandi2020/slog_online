<?php

namespace Tests\Unit\Factories;

use App\Entities\Networks\Network;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class NetworkModelFactoryTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function network_factory()
    {
        // Random Network Factory
        $network = factory(Network::class)->create();

        $this->seeInDatabase('networks', [
            'id' => $network->id,
            'name' => $network->name,
            'address' => $network->address,
            'coordinate' => '0.0000,0.0000',
            'postal_code' => '70000',
            'phone' => '081234567890',
            'email' => $network->email,
        ]);

        $this->assertTrue(strlen($network->code) == 8);
        $this->assertTrue(in_array($network->type_id, range(1,4)));

        if (in_array($network->type_id, [1,2])) {
            $this->assertTrue(strlen($network->origin_city_id) == 4);
            $this->assertNull($network->origin_district_id);
        } else {
            $this->assertNotNull($network->origin_city_id);
            $this->assertTrue(strlen($network->origin_district_id) == 7);
        }
    }

    /** @test */
    public function province_network_factory()
    {
        $provinceNetwork = factory(Network::class)->states('province')->create();

        $this->seeInDatabase('networks', [
            'id' => $provinceNetwork->id,
            'type_id' => 1,
        ]);
        $this->assertTrue(strlen($provinceNetwork->code) == 8);
        $this->assertEquals($provinceNetwork->code, substr($provinceNetwork->origin_city_id, 0, 2) . '000000');
        $this->assertTrue(strlen($provinceNetwork->origin_city_id) == 4);
        $this->assertNull($provinceNetwork->origin_district_id);

    }

    /** @test */
    public function city_network_factory()
    {
        $cityNetwork = factory(Network::class)->states('city')->create();

        $this->seeInDatabase('networks', [
            'id' => $cityNetwork->id,
            'type_id' => 2,
        ]);
        $this->assertTrue(strlen($cityNetwork->code) == 8);
        $this->assertEquals($cityNetwork->code, $cityNetwork->origin_city_id . '0000');
        $this->assertTrue(strlen($cityNetwork->origin_city_id) == 4);
        $this->assertNull($cityNetwork->origin_district_id);
    }

    /** @test */
    public function district_network_factory()
    {
        $districtNetwork = factory(Network::class)->states('district')->create();

        $this->seeInDatabase('networks', [
            'id' => $districtNetwork->id,
            'type_id' => 3,
        ]);
        $this->assertTrue(strlen($districtNetwork->code) == 8);
        $this->assertEquals($districtNetwork->code, $districtNetwork->origin_district_id . '0');
        $this->assertNotNull($districtNetwork->origin_city_id);
        $this->assertTrue(strlen($districtNetwork->origin_district_id) == 7);
    }

    /** @test */
    public function outlet_factory_class()
    {
        $outletNetwork = factory(Network::class)->states('outlet')->create();

        $this->seeInDatabase('networks', [
            'id' => $outletNetwork->id,
            'type_id' => 4,
        ]);
        $this->assertTrue(strlen($outletNetwork->code) == 8);
        $this->assertEquals(substr($outletNetwork->code, 0, 7), $outletNetwork->origin_district_id);
        $this->assertTrue(in_array(substr($outletNetwork->code, -1), range(1,9)));
        $this->assertNotNull($outletNetwork->origin_city_id);
        $this->assertTrue(strlen($outletNetwork->origin_district_id) == 7);
    }
}
