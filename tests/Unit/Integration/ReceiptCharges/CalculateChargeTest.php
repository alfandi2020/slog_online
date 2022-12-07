<?php

namespace Tests\Unit\Integration\ReceiptCharges;

use Tests\TestCase;
use App\Entities\Services\Rate;
use App\Entities\Services\Service;
use App\Services\ChargeCalculator;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalculateChargeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_calculate_city_to_city_charge()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(10000, $calculator->getCharge());

        $receiptQuery['charged_weight'] = 2;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
    }

    /** @test */
    public function it_can_calculate_city_to_city_charge_by_receipt_query()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'rate_kg'    => null,
            'rate_pc'    => 10000,
            'service_id' => 11,
        ]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['pcs_count'] = 2;
        $receiptQuery['charged_on'] = 2;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
        $this->assertEquals('Express', $calculator->getService());
        $this->assertEquals($rate->origin->name, $calculator->getOrigin());
        $this->assertEquals($rate->destination->name, $calculator->getDestination());
    }

    /** @test */
    public function it_can_has_to_array_method()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'rate_kg'    => null,
            'rate_pc'    => 10000,
            'service_id' => 11,
        ]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['pcs_count'] = 2;
        $receiptQuery['charged_on'] = 2;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $targetedArray = $this->getTargettedArray($rate);
        $targetedArray['pcs_count'] = 2;
        $targetedArray['charged_on'] = 2;
        $targetedArray['base_rate'] = 10000;
        $targetedArray['base_charge'] = 20000;
        $targetedArray['subtotal'] = 20000;
        $targetedArray['total'] = 20000;

        $this->assertEquals($targetedArray, $calculator->toArray());
    }

    /** @test */
    public function it_returns_correct_to_array_method_if_rate_is_0_or_null()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'rate_kg'    => null,
            'rate_pc'    => 10000,
            'service_id' => 11,
        ]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['pcs_count'] = 2;
        $receiptQuery['charged_on'] = 1;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $targetedArray = $this->getTargettedArray($rate);
        $targetedArray['pcs_count'] = 2;
        $targetedArray['charged_on'] = 1;
        $targetedArray['base_rate'] = 0;
        $targetedArray['base_charge'] = 0;
        $targetedArray['subtotal'] = 0;
        $targetedArray['total'] = 0;

        $this->assertEquals($targetedArray, $calculator->toArray());
    }

    /** @test */
    public function it_has_rate_model_as_property()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals($rate->toArray(), $calculator->rate->toArray());
    }

    /** @test */
    public function it_returns_0_if_no_rate_found()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['dest_city_id' => 1102, 'rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);
        $receiptQuery['dest_city_id'] = 1101;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(0, $calculator->getCharge());

        $receiptQuery['charged_weight'] = 2;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(0, $calculator->getCharge());

        $receiptQuery['dest_city_id'] = 1102;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
    }

    /** @test */
    public function it_can_calculate_city_to_district_charge()
    {
        $rate = factory(Rate::class, 'city_to_district')->create(['rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['charged_weight'] = 2;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
    }

    /** @test */
    public function it_can_calculate_district_to_city_charge()
    {
        $rate = factory(Rate::class, 'district_to_city')->create(['rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['charged_weight'] = 2;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
    }

    /** @test */
    public function it_can_calculate_district_to_district_charge()
    {
        $rate = factory(Rate::class, 'district_to_district')->create(['rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['charged_weight'] = 2;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(20000, $calculator->getCharge());
    }

    /** @test */
    public function rate_not_found_if_no_district_to_district_rate()
    {
        // asked: district to district; exists: district to city; then applied rate : district to city
        $rate = factory(Rate::class, 'district_to_city')->create(['dest_city_id' => 1101, 'rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);
        $receiptQuery['dest_district_id'] = 1101010;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertEquals(0, $calculator->getCharge());
        $this->assertNull($calculator->rate);
    }

    /** @test */
    public function rate_not_found_charge_if_no_city_to_district_rate()
    {
        // asked: city to district; exists: city to city; then applied rate : city to city
        $rate = factory(Rate::class, 'city_to_city')->create(['dest_city_id' => 1101, 'rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);
        $receiptQuery['dest_district_id'] = 1101010;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertEquals(0, $calculator->getCharge());
        $this->assertNull($calculator->rate);
    }

    /** @test */
    public function rate_not_found_charge_if_no_district_to_city_rate()
    {
        // asked: district to city; exists: city to city; then applied rate : city to city
        $rate = factory(Rate::class, 'city_to_city')->create(['orig_city_id' => 1101, 'rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);
        $receiptQuery['orig_district_id'] = 1101010;

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);

        $this->assertEquals(0, $calculator->getCharge());
        $this->assertNull($calculator->rate);
    }

    /** @test */
    public function it_can_calculate_city_to_city_charge_with_min_weight()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['rate_kg' => 10000, 'min_weight' => 5]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(50000, $calculator->getCharge());

        $receiptQuery['charged_weight'] = 2;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(50000, $calculator->getCharge());

        $receiptQuery['charged_weight'] = 6;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(60000, $calculator->getCharge());
    }

    /** @test */
    public function it_can_calculate_city_to_city_charge_with_admin_fee()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['rate_kg' => 10000]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(10000, $calculator->getCharge());

        $receiptQuery['admin_fee'] = 1;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(12000, $calculator->getCharge());
    }

    /** @test */
    public function it_returns_0_if_pc_rate_rate_found()
    {
        $rate = factory(Rate::class, 'city_to_city')->create(['dest_city_id' => 1102, 'rate_kg' => 10000, 'rate_pc' => null]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $receiptQuery['pcs_count'] = 2;
        $receiptQuery['charged_on'] = 2;
        $receiptQuery['discount'] = 500;
        $receiptQuery['packing_cost'] = 1000;

        $receiptQuery['dest_city_id'] = 1101;
        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals(0, $calculator->getCharge());
    }

    /** @test */
    public function it_has_properties()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'orig_city_id' => 6371,
            'dest_city_id' => 1101,
            'service_id'   => 11,
        ]);

        $receiptQuery = $this->getReceiptQuery($rate);

        $calculator = (new ChargeCalculator)->calculateByReceiptQuery($receiptQuery);
        $this->assertEquals('Kota Banjarmasin', $calculator->getOrigin());
        $this->assertEquals('Kab. Simeulue', $calculator->getDestination());
        $this->assertEquals(Service::getNameById(11), $calculator->getService());
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
            'packing_cost'     => 0,
            'add_cost'         => 0,

            'rate_id'        => $rate->id,
            'insurance_cost' => 0,
            'base_rate'      => $rate->rate_kg,
            'base_charge'    => $rate->rate_kg,
            'subtotal'       => $rate->rate_kg,
            'admin_fee'      => 0,
            'total'          => $rate->rate_kg,
        ];
    }
}
