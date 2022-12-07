<?php

namespace Tests\Unit\Integration\ReceiptCharges;

use App\Entities\Services\Rate;
use App\Services\ChargeCalculator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CalculateCustomerCharge extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_retrieve_customer_special_rate_for_charge_calculation()
    {
        $rate = $this->createRate();
        $customerRate = $this->createRate([
            'orig_city_id' => $rate->orig_city_id,
            'dest_city_id' => $rate->dest_city_id,
            'pack_type_id' => $rate->pack_type_id,
            'rate_kg'      => 10000,
        ], 'customer_city_to_city');

        $this->assertCount(2, Rate::all());
        $calculator = new ChargeCalculator;
        $calculator->calculateByReceiptQuery($this->getReceiptQuery($customerRate));
        $this->assertEquals(10000, $calculator->getCharge());
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['subtotal']);
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['total']);
        $this->assertEquals($customerRate->id, $calculator->rate->id);
        $this->assertEquals($customerRate->origin->name, $calculator->getOrigin());
        $this->assertEquals($customerRate->destination->name, $calculator->getDestination());
    }

    /** @test */
    public function it_can_retrieve_base_rate_for_charge_calculation_if_no_customer_special_rate()
    {
        $rate = $this->createRate(['customer_id' => 0]);

        $this->assertCount(1, Rate::all());

        $calculator = new ChargeCalculator;

        $receiptQuery = $this->getReceiptQuery($rate);
        $receiptQuery['customer_id'] = 1;

        $calculator->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['subtotal']);
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['total']);
        $this->assertEquals($rate->id, $calculator->rate->id);
        $this->assertEquals($rate->origin->name, $calculator->getOrigin());
        $this->assertEquals($rate->destination->name, $calculator->getDestination());
    }

    /** @test */
    public function it_can_retrieve_customer_special_rate_with_discount_for_charge_calculation()
    {
        $rate = $this->createRate();
        $customerRate = $this->createRate([
            'orig_city_id' => $rate->orig_city_id,
            'dest_city_id' => $rate->dest_city_id,
            'pack_type_id' => $rate->pack_type_id,
            'rate_kg'      => 10000,
        ], 'customer_city_to_city');

        $this->assertCount(2, Rate::all());
        $calculator = new ChargeCalculator;
        $receiptQuery = $this->getReceiptQuery($customerRate);
        $receiptQuery['discount'] = 1000;
        $calculator->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(9000, $calculator->getCharge());
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['subtotal']);
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['total']);
        $this->assertEquals(1000, $calculator->toArray()['discount']);
    }

    /** @test */
    public function it_can_retrieve_customer_special_rate_with_add_costs_for_charge_calculation()
    {
        $rate = $this->createRate();
        $customerRate = $this->createRate([
            'orig_city_id' => $rate->orig_city_id,
            'dest_city_id' => $rate->dest_city_id,
            'pack_type_id' => $rate->pack_type_id,
            'rate_kg'      => 10000,
        ], 'customer_city_to_city');

        $this->assertCount(2, Rate::all());
        $calculator = new ChargeCalculator;
        $receiptQuery = $this->getReceiptQuery($customerRate);
        $receiptQuery['packing_cost'] = 1000;
        $calculator->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(11000, $calculator->getCharge());
        $this->assertEquals(10000, $calculator->toArray()['subtotal']);
        $this->assertEquals($calculator->getCharge(), $calculator->toArray()['total']);
        $this->assertEquals(1000, $calculator->toArray()['packing_cost']);
    }

    private function createRate($overrides = [], $type = 'city_to_city')
    {
        $overrides = array_merge(['rate_kg' => 20000], $overrides);
        return factory(Rate::class, $type)->create($overrides);
    }

    private function getReceiptQuery(Rate $rate)
    {
        return [
            'customer_id'      => $rate->customer_id,
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->orig_district_id,
            'service_id'       => $rate->service_id,
            'pcs_count'        => 2,
            'charged_weight'   => 1,
            'charged_on'       => 1, // 1: weight, 2:item
            'pack_type_id'     => $rate->pack_type_id,
            'package_value'    => '',
            'be_insured'       => 0,
            'discount'         => 0,
            'packing_cost'     => 0,
            'admin_fee'        => 0,
        ];
    }
}
