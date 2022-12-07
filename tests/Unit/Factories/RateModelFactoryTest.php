<?php

namespace Tests\Unit\Factories;

use App\Entities\Customers\Customer;
use App\Entities\References\Reference;
use App\Entities\Services\Rate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class RateModelFactoryTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function rate_factory()
    {
        $rate = factory(Rate::class)->create();

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => 0,
            'service_id' => $rate->service_id,
            'pack_type_id' => $rate->pack_type_id,
            'orig_city_id' => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id' => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);
    }

    /** @test */
    public function city_to_city_rate_factory()
    {
        $rate = factory(Rate::class, 'city_to_city')->create();

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => 0,
            'service_id' => $rate->service_id,
            'orig_city_id' => $rate->orig_city_id,
            'orig_district_id' => 0,
            'dest_city_id' => $rate->dest_city_id,
            'dest_district_id' => 0,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertTrue(strlen($rate->orig_city_id) == 4);
        $this->assertTrue(strlen($rate->dest_city_id) == 4);
        $this->assertEquals(0, $rate->orig_district_id);
        $this->assertEquals(0, $rate->dest_district_id);
    }

    /** @test */
    public function city_to_district_rate_factory()
    {
        $rate = factory(Rate::class, 'city_to_district')->create();

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => 0,
            'service_id' => $rate->service_id,
            'orig_city_id' => $rate->orig_city_id,
            'orig_district_id' => 0,
            'dest_city_id' => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertTrue(strlen($rate->orig_city_id) == 4);
        $this->assertTrue(strlen($rate->dest_city_id) == 4);
        $this->assertTrue(strlen($rate->dest_district_id) == 7);
        $this->assertEquals(0, $rate->orig_district_id);
        $this->assertNotEquals(0, $rate->dest_district_id);
    }

    /** @test */
    public function district_to_city_rate_factory()
    {
        $rate = factory(Rate::class, 'district_to_city')->create();

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => 0,
            'service_id' => $rate->service_id,
            'orig_city_id' => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id' => $rate->dest_city_id,
            'dest_district_id' => 0,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertTrue(strlen($rate->orig_city_id) == 4);
        $this->assertTrue(strlen($rate->dest_city_id) == 4);
        $this->assertTrue(strlen($rate->orig_district_id) == 7);
        $this->assertNotEquals(0, $rate->orig_district_id);
        $this->assertEquals(0, $rate->dest_district_id);
    }

    /** @test */
    public function district_to_district_rate_factory()
    {
        $rate = factory(Rate::class, 'district_to_district')->create();

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => 0,
            'service_id' => $rate->service_id,
            'orig_city_id' => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id' => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertTrue(strlen($rate->orig_city_id) == 4);
        $this->assertTrue(strlen($rate->dest_city_id) == 4);
        $this->assertTrue(strlen($rate->orig_district_id) == 7);
        $this->assertTrue(strlen($rate->dest_district_id) == 7);
        $this->assertNotEquals(0, $rate->orig_district_id);
        $this->assertNotEquals(0, $rate->dest_district_id);
    }

    /** @test */
    public function rate_has_origin_and_destination()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['orig_city_id' => 6371, 'dest_city_id' => 1101]);
        $this->assertEquals('Kota Banjarmasin', $rate->originName());
        $this->assertEquals('Kab. Simeulue', $rate->destinationName());

        $rate = factory(Rate::class, 'city_to_district')->create([
            'orig_city_id' => 6371,
            'dest_city_id' => 1101,
            'dest_district_id' => 1101010
        ]);
        $this->assertEquals('Kota Banjarmasin', $rate->originName());
        $this->assertEquals('Kec. Teupah Selatan', $rate->destinationName());

        $rate = factory(Rate::class, 'district_to_city')->create([
            'orig_city_id' => 6371,
            'orig_district_id' => 6371020,
            'dest_city_id' => 1101
        ]);
        $this->assertEquals('Kec. Banjarmasin Timur', $rate->originName());
        $this->assertEquals('Kab. Simeulue', $rate->destinationName());

        $rate = factory(Rate::class, 'district_to_district')->create([
            'orig_city_id' => 6371,
            'orig_district_id' => 6371020,
            'dest_city_id' => 1101,
            'dest_district_id' => 1101010
        ]);
        $this->assertEquals('Kec. Banjarmasin Timur', $rate->originName());
        $this->assertEquals('Kec. Teupah Selatan', $rate->destinationName());
    }

    /** @test */
    public function a_rate_has_origin_province_id_and_destination_province_id()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['orig_city_id' => 6371, 'dest_city_id' => 1101]);
        $this->assertEquals('Kota Banjarmasin', $rate->originName());
        $this->assertEquals('Kab. Simeulue', $rate->destinationName());

        $this->assertEquals(63, $rate->orig_prov_id);
        $this->assertEquals(11, $rate->dest_prov_id);

        $this->assertEquals('KALIMANTAN SELATAN', $rate->originProvince->name);
        $this->assertEquals('ACEH', $rate->destinationProvince->name);
    }

    /** @test */
    public function a_rate_has_package_type()
    {
        $type = Reference::find(2);
        $rate = factory(Rate::class, 'city_to_city')->create(['orig_city_id' => 6371, 'dest_city_id' => 1101, 'pack_type_id' => $type->id]);
        $this->assertEquals('Dokumen', $rate->packType->name);
        $this->assertEquals('Dokumen', $rate->packTypeName());
    }

    /** @test */
    public function customer_city_to_city_rate_factory()
    {
        $rate = factory(Rate::class, 'customer_city_to_city')->create([
            'orig_city_id' => 6371,
            'dest_city_id' => 1101,
        ]);

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => $rate->customer_id,
            'service_id' => $rate->service_id,
            'orig_city_id' => 6371,
            'orig_district_id' => 0,
            'dest_city_id' => 1101,
            'dest_district_id' => 0,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertNotEquals(0, $rate->customer_id);
    }

    /** @test */
    public function customer_city_to_district_rate_factory()
    {
        $rate = factory(Rate::class, 'customer_city_to_district')->create([
            'orig_city_id' => 6371,
            'dest_city_id' => 1101,
            'dest_district_id' => 1101010,
        ]);

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => $rate->customer_id,
            'service_id' => $rate->service_id,
            'orig_city_id' => 6371,
            'orig_district_id' => 0,
            'dest_city_id' => 1101,
            'dest_district_id' => 1101010,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertNotEquals(0, $rate->customer_id);
    }

    /** @test */
    public function customer_district_to_city_rate_factory()
    {
        $rate = factory(Rate::class, 'customer_district_to_city')->create([
            'orig_city_id' => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id' => 1101,
            'dest_district_id' => 0,
        ]);

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => $rate->customer_id,
            'service_id' => $rate->service_id,
            'orig_city_id' => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id' => 1101,
            'dest_district_id' => 0,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertNotEquals(0, $rate->customer_id);
    }

    /** @test */
    public function customer_district_to_district_rate_factory()
    {
        $rate = factory(Rate::class, 'customer_district_to_district')->create([
            'orig_city_id' => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id' => 1101,
            'dest_district_id' => 1101010,
        ]);

        $this->seeInDatabase('rates', [
            'id' => $rate->id,
            'customer_id' => $rate->customer_id,
            'service_id' => $rate->service_id,
            'orig_city_id' => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id' => 1101,
            'dest_district_id' => 1101010,
            'rate_kg' => $rate->rate_kg,
            'rate_pc' => $rate->rate_pc,
            'min_weight' => $rate->min_weight,
            'max_weight' => $rate->max_weight,
            'pack_type_id' => $rate->pack_type_id,
            'etd' => $rate->etd,
            'notes' => $rate->notes,
        ]);

        $this->assertNotEquals(0, $rate->customer_id);
    }
}
