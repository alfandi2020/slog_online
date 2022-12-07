<?php

namespace Tests\Feature\Api;

use App\Entities\Services\Rate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ApiGetChargeTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function get_charge_calculation_on_receipt_draft()
    {
        $salesCounter = $this->loginAsSalesCounter();

        $rate = factory(Rate::class, 'city_to_city')->create([
            'orig_city_id' => $salesCounter->network->origin_city_id,
            'rate_kg'      => 10000,
            'rate_pc'      => 10000,
        ]);

        $response = $this->postJson(route("receipts.get-charge-calculation"), [
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'service_id'       => $rate->service_id,
            'customer_id'      => '',
            'pcs_count'        => 1,
            'items_count'      => 1,
            'charged_on'       => 1,
            'pack_type_id'     => 1,
            'charged_weight'   => 1,
            'packing_cost'     => 0,
            'add_cost'         => 0,
            'admin_fee'        => 0,
            'package_value'    => 0,
            'be_insured'       => 0,
            'discount'         => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'orig_city_id'           => $rate->orig_city_id,
            'orig_district_id'       => $rate->orig_district_id,
            'dest_city_id'           => $rate->dest_city_id,
            'dest_district_id'       => $rate->dest_district_id,
            'service_id'             => $rate->service_id,
            'admin_fee'              => 0,
            'base_charge'            => 10000,
            'base_rate'              => 10000,
            'be_insured'             => 0,
            'charged_on'             => 1,
            'charged_weight'         => 1,
            'customer_id'            => null,
            'discount'               => 0,
            'display_admin_fee'      => '-',
            'display_base_charge'    => 'Rp. 10.000',
            'display_discount'       => '-',
            'display_insurance_cost' => '-',
            'display_packing_cost'   => '-',
            'display_add_cost'       => '-',
            'display_subtotal'       => 'Rp. 10.000',
            'display_total'          => 'Rp. 10.000',
            'insurance_cost'         => 0,
            'items_count'            => 1,
            'message'                => '',
            'pack_type_id'           => 1,
            'package_value'          => 0,
            'packing_cost'           => 0,
            'add_cost'               => 0,
            'pcs_count'              => 1,
            'rate_id'                => $rate->id,
            'subtotal'               => 10000,
            'success'                => true,
            'total'                  => 10000,
        ]);
    }

    /** @test */
    public function get_charge_calculation_on_receipt_draft_by_charge_on_type()
    {
        $salesCounter = $this->loginAsSalesCounter();

        factory(Rate::class, 'city_to_district')->create([
            'orig_city_id'     => $salesCounter->network->origin_city_id,
            'dest_city_id'     => 6372,
            'dest_district_id' => 6372010,
            'rate_kg'          => 111,
            'rate_pc'          => 222,
            'service_id'       => 14,
        ]);

        $rate = factory(Rate::class, 'city_to_city')->create([
            'orig_city_id' => $salesCounter->network->origin_city_id,
            'dest_city_id' => 6372,
            'rate_kg'      => 333,
            'rate_pc'      => 444,
            'service_id'   => 14,
        ]);

        $response = $this->postJson(route("receipts.get-charge-calculation"), [
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'service_id'       => 14,
            'customer_id'      => '',
            'pcs_count'        => 1,
            'items_count'      => 1,
            'charged_on'       => 2,
            'pack_type_id'     => 1,
            'charged_weight'   => 1,
            'packing_cost'     => 0,
            'admin_fee'        => 0,
            'add_cost'         => 1000,
            'package_value'    => 0,
            'be_insured'       => 0,
            'discount'         => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'orig_city_id'           => $rate->orig_city_id,
            'orig_district_id'       => $rate->orig_district_id,
            'dest_city_id'           => $rate->dest_city_id,
            'dest_district_id'       => $rate->dest_district_id,
            'service_id'             => 14,
            'admin_fee'              => 0,
            'base_charge'            => 444,
            'base_rate'              => 444,
            'be_insured'             => 0,
            'charged_on'             => 2,
            'charged_weight'         => 1,
            'customer_id'            => null,
            'discount'               => 0,
            'display_admin_fee'      => '-',
            'display_base_charge'    => 'Rp. 444',
            'display_discount'       => '-',
            'display_insurance_cost' => '-',
            'display_packing_cost'   => '-',
            'display_add_cost'       => 'Rp. 1.000',
            'display_subtotal'       => 'Rp. 444',
            'display_total'          => 'Rp. 1.444',
            'insurance_cost'         => 0,
            'items_count'            => 1,
            'message'                => '',
            'pack_type_id'           => 1,
            'package_value'          => 0,
            'packing_cost'           => 0,
            'add_cost'               => 1000,
            'pcs_count'              => 1,
            'rate_id'                => $rate->id,
            'subtotal'               => 444,
            'success'                => true,
            'total'                  => 1444,
        ]);
    }
}
