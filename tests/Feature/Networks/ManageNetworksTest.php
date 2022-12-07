<?php

namespace Tests\Feature\Networks;

use App\Entities\Customers\Customer;
use App\Entities\Networks\Network;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ManageNetworksTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_create_new_province_network()
    {
        $this->loginAsAdmin();
        $this->visit(route('admin.networks.create'));
        $this->select(1, 'type_id');
        $this->type('Cabang Baru', 'name');
        $this->type('Alamat cabang baru', 'address');
        $this->type('0.0000,0.0000', 'coordinate');
        $this->type('70000', 'postal_code');
        $this->type('081234567890', 'phone');
        $this->type('cabangbaru@mail.com', 'email');
        $this->select(11, 'province_id');
        $this->press(trans('network.create'));

        $this->seePageIs(route('admin.networks.create'));
        $this->select(1101, 'origin_city_id');
        $this->press(trans('network.create'));

        $this->see(trans('network.created'));
        $this->seeInDatabase('networks', [
            'type_id' => 1,
            'code' => '11000000',
            'name' => 'Cabang Baru',
            'address' => 'Alamat cabang baru',
            'coordinate' => '0.0000, 0.0000',
            'postal_code' => '70000',
            'phone' => '081234567890',
            'email' => 'cabangbaru@mail.com',
            'origin_city_id' => 1101,
            'origin_district_id' => null,
        ]);
    }

    /** @test */
    public function admin_can_create_new_city_network()
    {
        $this->loginAsAdmin();
        $this->visit(route('admin.networks.create'));
        $this->select(2, 'type_id');
        $this->type('Cabang Baru', 'name');
        $this->type('Alamat cabang baru', 'address');
        $this->type('0.0000,0.0000', 'coordinate');
        $this->type('70000', 'postal_code');
        $this->type('081234567890', 'phone');
        $this->type('cabangbaru@mail.com', 'email');
        $this->select(11, 'province_id');
        $this->press(trans('network.create'));

        $this->seePageIs(route('admin.networks.create'));
        $this->select(1101, 'origin_city_id');
        $this->press(trans('network.create'));

        $this->see(trans('network.created'));
        $this->seeInDatabase('networks', [
            'type_id' => 2,
            'code' => '11010000',
            'origin_city_id' => 1101,
            'origin_district_id' => null,
        ]);
    }

    /** @test */
    public function admin_can_create_new_district_network()
    {
        $this->loginAsAdmin();
        $this->visit(route('admin.networks.create'));
        $this->select(3, 'type_id');
        $this->type('Cabang Baru', 'name');
        $this->type('Alamat cabang baru', 'address');
        $this->type('0.0000,0.0000', 'coordinate');
        $this->type('70000', 'postal_code');
        $this->type('081234567890', 'phone');
        $this->type('cabangbaru@mail.com', 'email');
        $this->select(11, 'province_id');
        $this->press(trans('network.create'));

        $this->seePageIs(route('admin.networks.create'));
        $this->select(1101, 'origin_city_id');
        $this->press(trans('network.create'));

        $this->seePageIs(route('admin.networks.create'));
        $this->select(1101010, 'origin_district_id');
        $this->press(trans('network.create'));

        $this->see(trans('network.created'));
        $this->seeInDatabase('networks', [
            'type_id' => 3,
            'code' => '11010100',
            'origin_city_id' => 1101,
            'origin_district_id' => 1101010,
        ]);
    }

    /** @test */
    public function admin_can_create_new_outlet_network()
    {
        $existingOutlet = factory(Network::class)->states('outlet')->create([
            'type_id' => 4,
            'code' => '11010101',
            'origin_city_id' => 1101,
            'origin_district_id' => 1101010
        ]);

        $this->loginAsAdmin();

        $this->visit(route('admin.networks.create'));
        $this->select(4, 'type_id');
        $this->type('Cabang Baru', 'name');
        $this->type('Alamat cabang baru', 'address');
        $this->type('0.0000,0.0000', 'coordinate');
        $this->type('70000', 'postal_code');
        $this->type('081234567890', 'phone');
        $this->type('cabangbaru@mail.com', 'email');
        $this->select(11, 'province_id');
        $this->press(trans('network.create'));

        $this->seePageIs(route('admin.networks.create'));
        $this->select(1101, 'origin_city_id');
        $this->press(trans('network.create'));

        $this->seePageIs(route('admin.networks.create'));
        $this->select(1101010, 'origin_district_id');
        $this->press(trans('network.create'));

        $this->see(trans('network.created'));
        $this->seeInDatabase('networks', [
            'type_id' => 4,
            'code' => '11010102',
            'origin_city_id' => 1101,
            'origin_district_id' => 1101010,
        ]);

        $this->assertEquals(2, \DB::table('networks')->where(['type_id' => 4, 'origin_district_id' => 1101010])->count());
    }

    /** @test */
    public function admin_can_see_networks_list()
    {
        $this->loginAsAdmin();
        $this->visit(route('admin.networks.index'));
    }

    /** @test */
    public function admin_can_see_a_network()
    {
        $this->loginAsAdmin();
        $network = factory(Network::class)->states('province')->create(['name' => 'BAM Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $this->visit(route('admin.networks.show', $network->id));
    }

    /** @test */
    public function admin_can_edit_a_network()
    {
        $this->loginAsAdmin();
        $network = factory(Network::class)->states('district')->create([
            'code' => '11000000',
            'origin_city_id' => 1101,
            'origin_district_id' => 1101010
        ]);

        $this->visit(route('admin.networks.edit', $network->id));
        $this->select(1, 'type_id');
        $this->type('Cabang Baru', 'name');
        $this->type('Alamat cabang baru', 'address');
        $this->type('0.0000,0.0000', 'coordinate');
        $this->type('70000', 'postal_code');
        $this->type('081234567890', 'phone');
        $this->type('cabangbaru@mail.com', 'email');
        $this->select(11, 'province_id');
        $this->select(1102, 'origin_city_id');
        $this->select(1101010, 'origin_district_id');

        $this->press(trans('network.update'));
        $this->see(trans('network.updated'));

        $this->seeInDatabase('networks', [
            'id' => $network->id,
            'type_id' => 1,
            'name' => 'Cabang Baru',
            'address' => 'Alamat cabang baru',
            'coordinate' => '0.0000, 0.0000',
            'postal_code' => '70000',
            'phone' => '081234567890',
            'email' => 'cabangbaru@mail.com',
            'origin_city_id' => 1102,
            'origin_district_id' => null,
        ]);
    }

    /** @test */
    public function admin_can_delete_an_empty_network()
    {
        $this->loginAsAdmin();
        $network = factory(Network::class)->states('province')->create(['name' => 'BAM Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $this->visit(route('admin.networks.delete', $network->id));
        $this->press(trans('network.delete'));
        $this->see(trans('network.deleted'));

        $this->dontSeeInDatabase('networks', [
            'id' => $network->id,
        ]);
    }
}
