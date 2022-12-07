<?php

namespace Tests\Feature\Customers;

use Tests\BrowserKitTestCase;
use App\Entities\Services\Rate;
use App\Entities\Customers\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageCustomerRatesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_add_customer_special_rate()
    {
        $this->loginAsAdmin();
        $customer = factory(Customer::class)->create();
        $this->visit(route('customers.rates.create', $customer->id));
        $this->submitForm(trans('customer.rate.create'), [
            'orig_city_id'     => 6371,
            'orig_district_id' => '',
            'dest_city_id'     => 6271,
            'dest_district_id' => '',
            'service_id'       => 21,
            'rate_kg'          => 10000,
            'rate_pc'          => '',
            'min_weight'       => 10,
            'pack_type_id'     => 1,
            'discount'         => '',
            'add_cost'         => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->seeInDatabase('rates', [
            'customer_id'      => $customer->id,
            'service_id'       => 21,
            'pack_type_id'     => 1,
            'orig_city_id'     => 6371,
            'orig_district_id' => 0,
            'dest_city_id'     => 6271,
            'dest_district_id' => 0,
            'rate_kg'          => 10000,
            'rate_pc'          => null,
            'min_weight'       => 10,
            'discount'         => null,
            'add_cost'         => null,
            'etd'              => '2-3',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function admin_can_edit_a_customer_special_rate()
    {
        $accountingUser = $this->loginAsAdmin();
        $customer = factory(Customer::class)->create(['network_id' => $accountingUser->network_id]);
        $customerRate = factory(Rate::class, 'customer')->create(['customer_id' => $customer->id, 'rate_kg' => 10000]);
        $this->visit(route('customers.rates.edit', [$customer->id, $customerRate->id]));
        $this->submitForm(trans('customer.rate.update'), [
            'rate_kg'      => 13000,
            'rate_pc'      => '',
            'min_weight'   => 10,
            'pack_type_id' => 1,
            'discount'     => '',
            'add_cost'     => '',
            'etd'          => '2-3',
            'notes'        => '',
        ]);

        $this->see(trans('rate.updated'));
        $this->seePageIs(route('customers.rates.edit', [$customer->id, $customerRate->id]));

        $this->seeInDatabase('rates', [
            'customer_id' => $customer->id,
            'rate_kg'     => 13000,
            'rate_pc'     => null,
            'min_weight'  => 10,
            'discount'    => null,
            'add_cost'    => null,
            'etd'         => '2-3',
            'notes'       => null,
        ]);
    }

    /** @test */
    public function admin_can_delete_a_customer_special_rate()
    {
        $accountingUser = $this->loginAsAdmin();
        $customer = factory(Customer::class)->create(['network_id' => $accountingUser->network_id]);
        $customerRate = factory(Rate::class, 'customer')->create(['customer_id' => $customer->id, 'rate_kg' => 10000]);
        $this->visit(route('customers.rates.edit', [$customer->id, $customerRate->id]));
        $this->click(trans('rate.delete'));
        $this->press(trans('app.delete_confirm_button'));

        $this->see(trans('rate.deleted'));
        $this->seePageIs(route('customers.rates.index', $customer->id));

        $this->dontSeeInDatabase('rates', [
            'id'          => $customerRate->id,
            'customer_id' => $customer->id,
        ]);
    }

    /** @test */
    public function admin_can_update_customer_rates_on_rate_index_page()
    {
        $origCityId = '3173';
        $destCityId = '3101';
        $adminUser = $this->loginAsAdmin();
        $rate = factory(Rate::class)->create([
            'orig_city_id'     => $origCityId,
            'orig_district_id' => 0,
            'dest_city_id'     => $destCityId,
            'dest_district_id' => 0,
            'service_id'       => '11',
            'customer_id'      => 0,
        ]);
        $customer = factory(Customer::class)->create();
        $this->visit(route('rates.index', [
            'region'       => '3',
            'service_id'   => '11',
            'orig_city_id' => $origCityId,
            'customer_id'  => $customer->id,
        ]));

        // update_origcityid_provinceid_serviceid_customerid
        $this->seeElement('input', ['id' => 'update_3173_31_11_'.$customer->id]);
        $this->submitForm('update_3173_31_11_'.$customer->id, [
            'rate' => [
                $destCityId => [
                    'kg'         => 10000,
                    'pc'         => 20000,
                    'min_weight' => 2,
                    'etd'        => '5',
                ],
            ],
        ]);

        $this->seeInDatabase('rates', [
            'customer_id'      => $customer->id,
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => $origCityId,
            'orig_district_id' => '0',
            'dest_city_id'     => $destCityId,
            'dest_district_id' => '0',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);

        $this->seeInDatabase('rates', [
            'customer_id'      => $customer->id,
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => $origCityId,
            'orig_district_id' => '0',
            'dest_city_id'     => $destCityId,
            'dest_district_id' => $destCityId.'010',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);

        $this->seeInDatabase('rates', [
            'customer_id'      => $customer->id,
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => $origCityId,
            'orig_district_id' => '0',
            'dest_city_id'     => $destCityId,
            'dest_district_id' => $destCityId.'020',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);
    }

    /** @test */
    public function admin_can_update_customer_rates_on_rate_index_page_with_dest_city()
    {
        $origCityId = '3173';
        $destCityId = '3101';
        $adminUser = $this->loginAsAdmin();
        $cityRate = factory(Rate::class)->create([
            'orig_city_id'     => $origCityId,
            'orig_district_id' => 0,
            'dest_city_id'     => $destCityId,
            'dest_district_id' => 0,
            'service_id'       => '11',
            'customer_id'      => 0,
        ]);
        $districtRate = factory(Rate::class)->create([
            'orig_city_id'     => $origCityId,
            'orig_district_id' => 0,
            'dest_city_id'     => $destCityId,
            'dest_district_id' => $destCityId.'010',
            'service_id'       => '11',
            'customer_id'      => 0,
        ]);
        $customer = factory(Customer::class)->create();
        $this->visit(route('rates.index', [
            'region'       => '3',
            'service_id'   => '11',
            'orig_city_id' => $origCityId,
            'dest_city_id' => $destCityId,
            'customer_id'  => $customer->id,
        ]));

        // update_origcityid_cityid_serviceid_customerid
        $updateButtonId = 'update_'.$origCityId.'_'.$destCityId.'_11_'.$customer->id;
        $this->seeElement('input', ['id' => $updateButtonId]);
        $this->submitForm($updateButtonId, [
            'rate' => [
                $destCityId.'010' => [
                    'kg'         => 10000,
                    'pc'         => 20000,
                    'min_weight' => 2,
                    'etd'        => '5',
                ],
            ],
        ]);

        $this->seeInDatabase('rates', [
            'customer_id'      => $customer->id,
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => $origCityId,
            'orig_district_id' => '0',
            'dest_city_id'     => $destCityId,
            'dest_district_id' => $destCityId.'010',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);
    }
}
