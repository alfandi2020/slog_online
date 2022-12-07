<?php

namespace Tests\Feature\Networks;

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Networks\Network;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class DeliveryUnitsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_create_new_delivery_unit()
    {
        $admin = $this->loginAsAdmin();
        $network = factory(Network::class)->states('province')->create(['name' => 'BAM Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $this->visit(route('admin.networks.delivery-units', $network->id));
        $this->click(trans('delivery_unit.create'));

        $this->seePageIs(route('admin.networks.delivery-units', [$network->id, 'action' => 'create']));

        $this->type('BAM-BDJ-01', 'name');
        $this->type('DA 1234 AB', 'plat_no');
        $this->select(1, 'type_id');
        $this->type('Operasional Distribusi Banjarmasin', 'description');
        $this->press(trans('delivery_unit.create'));

        $this->seePageIs(route('admin.networks.delivery-units', $network->id));
        $this->see(trans('delivery_unit.created'));

        $this->seeInDatabase('delivery_units', [
            'name' => 'BAM-BDJ-01',
            'description' => 'Operasional Distribusi Banjarmasin',
            'plat_no' => 'DA 1234 AB',
            'type_id' => 1,
            'network_id' => $network->id,
        ]);
    }
    /** @test */
    public function admin_can_edit_a_delivery_unit()
    {
        $admin = $this->loginAsAdmin();
        $network = factory(Network::class)->states('province')->create(['name' => 'BAM Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $deliveryUnit = factory(DeliveryUnit::class)->create(['plat_no' => 'DA 1234 AB','network_id' => $network->id]);

        $this->visit(route('admin.networks.delivery-units', $network->id));
        $this->click('edit-delivery_unit-' . $deliveryUnit->id);
        $this->seePageIs(route('admin.networks.delivery-units', [$network->id, 'action' => 'edit','id' => $deliveryUnit->id]));

        $this->type('BAM-BDJ-01', 'name');
        $this->type('DA 1234 AB', 'plat_no');
        $this->select(2, 'type_id');
        $this->type('Operasional Distribusi Banjarmasin', 'description');
        $this->press(trans('delivery_unit.update'));
        $this->see(trans('delivery_unit.updated'));

        $this->seeInDatabase('delivery_units', [
            'name' => 'BAM-BDJ-01',
            'plat_no' => 'DA 1234 AB',
            'type_id' => 2,
            'network_id' => $network->id,
            'description' => 'Operasional Distribusi Banjarmasin',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_delivery_unit()
    {
        $this->loginAsAdmin();
        $network = factory(Network::class)->states('province')->create(['name' => 'BAM Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $deliveryUnit = factory(DeliveryUnit::class)->create(['plat_no' => 'DA 1234 AB','network_id' => $network->id]);

        $this->visit(route('admin.networks.delivery-units', $network->id));
        $this->click('del-delivery_unit-' . $deliveryUnit->id);
        $this->seePageIs(route('admin.networks.delivery-units', [$network->id, 'action' => 'delete','id' => $deliveryUnit->id]));

        $this->press(trans('app.delete_confirm_button'));

        $this->dontSeeInDatabase('delivery_units', [
            'id' => $deliveryUnit->id
        ]);
    }
}
