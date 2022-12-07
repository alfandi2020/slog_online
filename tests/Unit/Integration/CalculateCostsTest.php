<?php

namespace Tests\Unit\Integration;

use App\Entities\Services\Rate;
use App\Services\CostsCalculator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CalculateCostsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_calculate_city_to_city_costs()
    {
        $rate1 = factory(Rate::class, 'city_to_city')->create([
            'service_id' => 11, 'rate_kg' => 10000
        ]);
        $rate2 = factory(Rate::class, 'city_to_city')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
        ]);

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($rate1->orig_city_id, $rate1->dest_city_id, $weight);
        $this->assertCount(2, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->where('service_id', 11)->first()->cost);
        $this->assertEquals(40000, $calculator->getRates()->where('service_id', 12)->first()->cost);
    }

    /** @test */
    public function it_can_calculate_city_to_district_costs()
    {
        $rate1 = factory(Rate::class, 'city_to_district')->create([
            'service_id' => 11, 'rate_kg' => 10000
        ]);
        $rate2 = factory(Rate::class, 'city_to_district')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'dest_district_id' => $rate1->dest_district_id,
        ]);

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($rate1->orig_city_id, $rate1->dest_district_id, $weight);
        $this->assertCount(2, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->where('service_id', 11)->first()->cost);
        $this->assertEquals(40000, $calculator->getRates()->where('service_id', 12)->first()->cost);
    }

    /** @test */
    public function it_can_calculate_district_to_city_costs()
    {
        $rate1 = factory(Rate::class, 'district_to_city')->create([
            'service_id' => 11, 'rate_kg' => 10000
        ]);
        $rate2 = factory(Rate::class, 'district_to_city')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'orig_district_id' => $rate1->orig_district_id,
            'dest_city_id' => $rate1->dest_city_id,
        ]);

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($rate1->orig_district_id, $rate1->dest_city_id, $weight);
        $this->assertCount(2, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->where('service_id', 11)->first()->cost);
        $this->assertEquals(40000, $calculator->getRates()->where('service_id', 12)->first()->cost);
    }

    /** @test */
    public function it_can_calculate_district_to_district_costs()
    {
        $rate1 = factory(Rate::class, 'district_to_district')->create([
            'service_id' => 11, 'rate_kg' => 10000
        ]);
        $rate2 = factory(Rate::class, 'district_to_district')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'orig_district_id' => $rate1->orig_district_id,
            'dest_city_id' => $rate1->dest_city_id,
            'dest_district_id' => $rate1->dest_district_id,
        ]);

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($rate1->orig_district_id, $rate1->dest_district_id, $weight);
        $this->assertCount(2, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->where('service_id', 11)->first()->cost);
        $this->assertEquals(40000, $calculator->getRates()->where('service_id', 12)->first()->cost);
    }

    /** @test */
    public function it_returns_0_if_no_rate_found()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['dest_city_id' => 1102, 'rate_kg' => 10000]);
        $origId = $rate->orig_city_id;
        $destId = 1101;

        $weight = 1; // in KG
        $calculator = (new CostsCalculator)->calculate($origId, $destId);
        $this->assertCount(0, $calculator->getRates());
    }

    /** @test */
    public function it_returns_district_to_city_charge_if_no_district_to_district_rate()
    {
        // asked: district to district; exists: district to city; then applied rate : district to city
        $rate = factory(Rate::class, 'district_to_city')->create(['dest_city_id' => 1101, 'rate_kg' => 10000]);
        $origId = $rate->orig_district_id;
        $destId = 1101010;

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($origId, $destId, $weight);
        $this->assertCount(1, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->first()->cost);
    }

    /** @test */
    public function it_returns_city_to_city_charge_if_no_city_to_district_rate()
    {
        // asked: city to district; exists: city to city; then applied rate : city to city
        $rate = factory(Rate::class, 'city_to_city')->create(['dest_city_id' => 1101, 'rate_kg' => 10000]);
        $origId = $rate->orig_city_id;
        $destId = 1101010;

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($origId, $destId, $weight);
        $this->assertCount(1, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->first()->cost);
    }

    /** @test */
    public function it_returns_city_to_city_charge_if_no_district_to_city_rate()
    {
        // asked: district to city; exists: city to city; then applied rate : city to city
        $rate = factory(Rate::class, 'city_to_city')->create(['orig_city_id' => 1101, 'rate_kg' => 10000]);
        $origId = 1101010;
        $destId = $rate->dest_city_id;

        $weight = 2; // in KG
        $calculator = (new CostsCalculator)->calculate($origId, $destId, $weight);
        $this->assertCount(1, $calculator->getRates());
        $this->assertEquals(20000, $calculator->getRates()->first()->cost);
    }

    /** @test */
    public function it_has_properties()
    {
        $calculator = (new CostsCalculator)->calculate(6371, 1101);
        $this->assertEquals('Kota Banjarmasin', $calculator->getOrigin());
        $this->assertEquals('Kab. Simeulue', $calculator->getDestination());
        $this->assertEquals(1, $calculator->getWeight());
    }
}
