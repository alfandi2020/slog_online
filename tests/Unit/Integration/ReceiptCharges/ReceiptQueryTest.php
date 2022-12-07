<?php

namespace Tests\Unit\Integration\ReceiptCharges;

use App\Entities\Services\Rate;
use App\Services\ChargeCalculator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptQueryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_make_draft_receipt_from_complete_receipt_query()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'rate_kg' => 10000,
            'rate_pc' => null,
        ]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $targetedArray = $this->getTargettedArray($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertEquals($targetedArray, $calculator->toArray());
        $this->assertEquals($calculator->rate->id, $rate->id);
    }

    /** @test */
    public function it_can_make_draft_receipt_from_minimum_receipt_query()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'rate_kg' => 10000,
            'rate_pc' => null,
        ]);

        $receiptQuery = [
            'customer_id'      => '',
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => '',
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => '',
            'service_id'       => $rate->service_id,
            'charged_weight'   => 1,
        ];

        $targetedArray = $this->getTargettedArray($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertEquals($targetedArray, $calculator->toArray());
        $this->assertEquals($calculator->rate->id, $rate->id);
    }

    /** @test */
    public function it_select_correct_city_to_city_rate_even_if_city_to_district_was_exists()
    {
        $cityToDistrictRate = factory(Rate::class)->create([
            'customer_id'      => 0,
            'service_id'       => '41',
            'orig_city_id'     => '6371',
            'orig_district_id' => '0',
            'dest_city_id'     => '6372',
            'dest_district_id' => '6372010',
            'rate_kg'          => 1000,
            'rate_pc'          => null,
        ]);

        $receiptQuery = [
            'customer_id'      => '',
            'service_id'       => '41',
            'orig_city_id'     => '6371',
            'orig_district_id' => '',
            'dest_city_id'     => '6372',
            'dest_district_id' => '',
            'charged_weight'   => 1,
        ];

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertNull($calculator->rate);
    }

    /** @test */
    public function it_doesnt_pickup_customer_rate_for_retail_customer_even_if_customer_rate_is_exist_but_base_rate_doesnt()
    {
        $customerRate = factory(Rate::class)->create([
            'customer_id'      => 1,
            'service_id'       => '14',
            'orig_city_id'     => '6371',
            'orig_district_id' => '0',
            'dest_city_id'     => '6372',
            'dest_district_id' => '0',
            'rate_kg'          => 1000,
            'rate_pc'          => null,
        ]);

        $receiptQuery = [
            'customer_id'      => '',
            'service_id'       => '14',
            'orig_city_id'     => '6371',
            'orig_district_id' => '',
            'dest_city_id'     => '6372',
            'dest_district_id' => '',
            'charged_weight'   => 1,
        ];

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertNull($calculator->rate);
        $this->assertEquals(0, $calculator->getCharge());
    }

    /** @test */
    public function it_pickup_base_rate_for_retail_customer_even_if_customer_rate_is_exist()
    {
        $customerRate = factory(Rate::class)->create([
            'customer_id'      => 1,
            'service_id'       => '14',
            'orig_city_id'     => '6371',
            'orig_district_id' => '0',
            'dest_city_id'     => '6372',
            'dest_district_id' => '0',
            'rate_kg'          => 1000,
            'rate_pc'          => null,
        ]);

        $baseCityToDistrictRate = factory(Rate::class)->create([
            'customer_id'      => 0,
            'service_id'       => '14',
            'orig_city_id'     => '6371',
            'orig_district_id' => '0',
            'dest_city_id'     => '6372',
            'dest_district_id' => '6372010',
            'rate_kg'          => 2000,
            'rate_pc'          => null,
        ]);

        $baseRate = factory(Rate::class)->create([
            'customer_id'      => 0,
            'service_id'       => '14',
            'orig_city_id'     => '6371',
            'orig_district_id' => '0',
            'dest_city_id'     => '6372',
            'dest_district_id' => '0',
            'rate_kg'          => 1000,
            'rate_pc'          => null,
        ]);

        $receiptQuery = [
            'customer_id'      => '',
            'service_id'       => '14',
            'orig_city_id'     => '6371',
            'orig_district_id' => '',
            'dest_city_id'     => '6372',
            'dest_district_id' => '',
            'charged_weight'   => 2,
        ];

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertNotNull($calculator->rate);
        $this->assertEquals(2000, $calculator->getCharge());
        $this->assertEquals($calculator->rate->id, $baseRate->id);
    }

    private function getReceiptQuery(Rate $rate)
    {
        return [
            'customer_id'      => '',
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id ?: '',
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id ?: '',
            'service_id'       => $rate->service_id,
            'pcs_count'        => 1,
            'charged_weight'   => 1,
            'charged_on'       => 1, // 1: weight, 2:item
            'pack_type_id'     => $rate->pack_type_id,
            'package_value'    => '',
            'be_insured'       => 0,
            'discount'         => 0,
            'packing_cost'     => 0,
            'add_cost'         => 0,
            'admin_fee'        => 0,
        ];
    }

    private function getTargettedArray(Rate $rate)
    {
        return [
            'customer_id'      => '',
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'service_id'       => $rate->service_id,
            'pcs_count'        => 1,
            'items_count'      => 1,
            'charged_weight'   => 1,
            'charged_on'       => 1, // 1: weight, 2:item
            'pack_type_id'     => $rate->pack_type_id,
            'package_value'    => '',
            'be_insured'       => 0,
            'discount'         => 0,

            'rate_id'        => $rate->id,
            'base_rate'      => $rate->rate_kg,
            'base_charge'    => $rate->rate_kg,
            'discount'       => 0,
            'subtotal'       => $rate->rate_kg,
            'insurance_cost' => 0,
            'packing_cost'   => 0,
            'add_cost'       => 0,
            'admin_fee'      => 0,
            'total'          => $rate->rate_kg,
        ];
    }

}
