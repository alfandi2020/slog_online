<?php

namespace Tests\Feature\Receipts;

use Tests\BrowserKitTestCase;
use App\Entities\Services\Rate;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageRatesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_update_rates_on_rate_index_page()
    {
        $adminUser = $this->loginAsAdmin();
        $this->visit(route('rates.index', ['region' => '3', 'orig_city_id' => '3173', 'service_id' => '11', 'customer_id' => '0']));

        // update_origcityid_provinceid_serviceid_customerid
        $this->seeElement('input', ['id' => 'update_3173_32_11_0']);
        $this->submitForm('update_3173_31_11_0', [
            'rate' => [
                '3101' => [
                    'kg'         => 10000,
                    'pc'         => 20000,
                    'min_weight' => 2,
                    'etd'        => '5',
                ],
            ],
        ]);

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => '3173',
            'orig_district_id' => '0',
            'dest_city_id'     => '3101',
            'dest_district_id' => '0',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => '3173',
            'orig_district_id' => '0',
            'dest_city_id'     => '3101',
            'dest_district_id' => '3101010',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => '3173',
            'orig_district_id' => '0',
            'dest_city_id'     => '3101',
            'dest_district_id' => '3101020',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);
    }

    /** @test */
    public function admin_can_update_rates_on_rate_index_page_with_dest_city()
    {
        $adminUser = $this->loginAsAdmin();
        $this->visit(route('rates.index', ['region' => '3', 'orig_city_id' => '3173', 'dest_city_id' => '3101', 'service_id' => '11', 'customer_id' => '0']));

        // update_origcityid_cityid_serviceid_customerid
        $this->seeElement('input', ['id' => 'update_3173_3101_11_0']);
        $this->submitForm('update_3173_3101_11_0', [
            'rate' => [
                '3101010' => [
                    'kg'         => 10000,
                    'pc'         => 20000,
                    'min_weight' => 2,
                    'etd'        => '5',
                ],
            ],
        ]);

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => '3173',
            'orig_district_id' => '0',
            'dest_city_id'     => '3101',
            'dest_district_id' => '3101010',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 2,
            'etd'              => '5',
        ]);
    }

    /** @test */
    public function admin_can_create_new_city_to_city_rate()
    {
        $this->loginAsAdmin();
        $this->visit(route('rates.create'));

        $this->submitForm(trans('rate.create'), [
            'service_id'   => 11,
            'orig_city_id' => 6371,
            'dest_city_id' => 1101,
            'rate_kg'      => 11000,
            'rate_pc'      => '',
            'etd'          => '2-3',
            'notes'        => '',
        ]);

        $this->seePageIs(route('rates.list'));
        $this->see(trans('rate.created'));

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => 6371,
            'orig_district_id' => 0,
            'dest_city_id'     => 1101,
            'dest_district_id' => 0,
            'rate_kg'          => 11000,
            'rate_pc'          => null,
            'min_weight'       => 1,
            'max_weight'       => null,
            'etd'              => '2-3',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function admin_can_create_new_city_to_district_rate()
    {
        $this->loginAsAdmin();
        $this->visit(route('rates.create'));

        $this->submitForm(trans('rate.create'), [
            'service_id'       => 11,
            'orig_city_id'     => 6371,
            'orig_district_id' => '',
            'dest_city_id'     => 1101,
            'dest_district_id' => '',
            'rate_kg'          => '',
            'rate_pc'          => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->submitForm(trans('rate.create'), [
            'service_id'       => 11,
            'orig_city_id'     => 6371,
            'orig_district_id' => '',
            'dest_city_id'     => 1101,
            'dest_district_id' => 1101010,
            'rate_kg'          => 11000,
            'rate_pc'          => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->seePageIs(route('rates.list'));
        $this->see(trans('rate.created'));

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => 6371,
            'orig_district_id' => 0,
            'dest_city_id'     => 1101,
            'dest_district_id' => 1101010,
            'rate_kg'          => 11000,
            'rate_pc'          => null,
            'min_weight'       => 1,
            'max_weight'       => null,
            'etd'              => '2-3',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function admin_can_create_new_district_to_city_rate()
    {
        $this->loginAsAdmin();
        $this->visit(route('rates.create'));

        $this->submitForm(trans('rate.create'), [
            'service_id'       => 11,
            'orig_city_id'     => 6371,
            'orig_district_id' => '',
            'dest_city_id'     => 1101,
            'dest_district_id' => '',
            'rate_kg'          => '',
            'rate_pc'          => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->submitForm(trans('rate.create'), [
            'service_id'       => 11,
            'orig_city_id'     => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id'     => 1101,
            'dest_district_id' => '',
            'rate_kg'          => 11000,
            'rate_pc'          => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->seePageIs(route('rates.list'));
        $this->see(trans('rate.created'));

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id'     => 1101,
            'dest_district_id' => 0,
            'rate_kg'          => 11000,
            'rate_pc'          => null,
            'min_weight'       => 1,
            'max_weight'       => null,
            'etd'              => '2-3',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function admin_can_create_new_district_to_district_rate()
    {
        $this->loginAsAdmin();
        $this->visit(route('rates.create'));

        $this->submitForm(trans('rate.create'), [
            'service_id'       => 11,
            'orig_city_id'     => 6371,
            'orig_district_id' => '',
            'dest_city_id'     => 1101,
            'dest_district_id' => '',
            'rate_kg'          => '',
            'rate_pc'          => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->submitForm(trans('rate.create'), [
            'service_id'       => 11,
            'orig_city_id'     => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id'     => 1101,
            'dest_district_id' => 1101010,
            'rate_kg'          => 11000,
            'rate_pc'          => '',
            'etd'              => '2-3',
            'notes'            => '',
        ]);

        $this->seePageIs(route('rates.list'));
        $this->see(trans('rate.created'));

        $this->seeInDatabase('rates', [
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => 6371,
            'orig_district_id' => 6371010,
            'dest_city_id'     => 1101,
            'dest_district_id' => 1101010,
            'rate_kg'          => 11000,
            'rate_pc'          => null,
            'min_weight'       => 1,
            'max_weight'       => null,
            'etd'              => '2-3',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function admin_can_edit_a_rate()
    {
        $this->loginAsAdmin();
        $rate = factory(Rate::class)->create();
        $this->visit(route('rates.edit', $rate->id));

        $this->submitForm(trans('rate.update'), [
            'rate_kg' => 10000,
            'rate_pc' => 10000,
            'notes'   => 'tambah harga kolian',
        ]);

        $this->seePageIs(route('rates.edit', $rate->id));
        $this->see(trans('rate.updated'));
        $this->seeInDatabase('rates', [
            'id'      => $rate->id,
            'rate_kg' => 10000,
            'rate_pc' => 10000,
            'notes'   => 'tambah harga kolian',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_rate()
    {
        $this->loginAsAdmin();
        $rate = factory(Rate::class)->create();
        $this->visit(route('rates.edit', $rate->id));
        $this->click(trans('rate.delete'));
        $this->press(trans('rate.delete'));

        $this->seePageIs(route('rates.list'));
        $this->see(trans('rate.deleted'));
        $this->notSeeInDatabase('rates', [
            'id' => $rate->id,
        ]);
    }

    /** @test */
    public function admin_can_delete_a_district_rate_from_rate_index_page()
    {
        $this->loginAsAdmin();
        $rate = factory(Rate::class)->create([
            'service_id'       => 11,
            'pack_type_id'     => 1,
            'orig_city_id'     => '3173',
            'orig_district_id' => '0',
            'dest_city_id'     => '3101',
            'dest_district_id' => '3101010',
            'rate_kg'          => 10000,
            'rate_pc'          => 20000,
            'min_weight'       => 1,
            'etd'              => '5',
        ]);
        $this->visit(route('rates.index', ['region' => '3', 'orig_city_id' => '3173', 'dest_city_id' => '3101', 'service_id' => '11', 'customer_id' => '0']));

        $this->seeElement('input', ['id' => 'update_3173_3101_11_0']);
        $this->submitForm('update_3173_3101_11_0', [
            'rate' => [
                '3101010' => [
                    'kg'         => '',
                    'pc'         => '',
                    'min_weight' => '',
                    'etd'        => '',
                ],
            ],
        ]);

        // $this->seePageIs(route('rates.index', ['customer_id' => '0', 'dest_city_id' => '3101', 'orig_city_id' => '3173', 'region' => '3', 'service_id' => '11']));
        // $this->see(trans('rate.deleted'));
        $this->notSeeInDatabase('rates', [
            'id' => $rate->id,
        ]);
    }
}
