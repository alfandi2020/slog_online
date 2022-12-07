<?php

namespace Tests\Feature\Services;

use App\Entities\Users\User;
use App\Entities\Services\Pickup;
use App\Entities\Customers\Customer;
use App\Entities\Networks\DeliveryUnit;
use Tests\BrowserKitTestCase as TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManagePickupsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_see_pickup_list_in_pickup_index_page()
    {
        $admin = $this->loginAsAdmin();

        $sameNetworkPickup = factory(Pickup::class)->create(['network_id' => $admin->network_id]);
        $otherNetworkPickup = factory(Pickup::class)->create(['network_id' => 2]);

        $this->visit(route('pickups.index'));
        $this->see($sameNetworkPickup->numberLink());
        $this->see($otherNetworkPickup->numberLink());
    }

    /** @test */
    public function warehouse_can_only_see_pickup_list_in_same_network()
    {
        $warehouse = $this->loginAsWarehouse();

        $sameNetworkPickup = factory(Pickup::class)->create(['network_id' => $warehouse->network_id]);
        $otherNetworkPickup = factory(Pickup::class)->create(['network_id' => 2]);

        $this->visit(route('pickups.index'));
        $this->see($sameNetworkPickup->numberLink());
        $this->dontSee($otherNetworkPickup->numberLink());
    }

    /** @test */
    public function user_can_create_a_pickup()
    {
        $admin = $this->loginAsWarehouse();
        $pickupCourier = User::find(7); // Seeded Courier Kalsel
        $customers = factory(Customer::class, 2)->create();
        $deliveryUnit = factory(DeliveryUnit::class)->create();
        $this->visit(route('pickups.index'));

        $this->click(trans('pickup.create'));
        $this->seePageIs(route('pickups.create'));

        $this->submitForm(trans('pickup.create'), [
            'courier_id'       => $pickupCourier->id,
            'delivery_unit_id' => $deliveryUnit->id,
            'customer_ids'     => $customers->pluck('id')->all(),
            'notes'            => 'Catatan pickup',
        ]);

        $this->see(trans('pickup.created'));

        $customerIds = $customers->pluck('id')->toArray();

        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => null,
                'pcs_count'      => null,
                'items_count'    => null,
                'weight_total'   => null,
                'notes'          => null,
            ];
        }

        $this->seeInDatabase('pickups', [
            'number'           => 'PU'.date('ym').'0001',
            'network_id'       => $admin->network_id,
            'courier_id'       => $pickupCourier->id,
            'creator_id'       => $admin->id,
            'customers'        => json_encode($formattedCustomerIds),
            'notes'            => 'Catatan pickup',
            'delivery_unit_id' => $deliveryUnit->id,
            'sent_at'          => null,
            'returned_at'      => null,
        ]);
    }

    /** @test */
    public function user_can_edit_a_pickup()
    {
        $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->create();
        $pickupCourier = User::find(7); // Seeded Courier Kalsel
        $customers = factory(Customer::class, 2)->create();
        $deliveryUnit = factory(DeliveryUnit::class)->create();

        $this->visit(route('pickups.show', $pickup));
        $this->click(trans('pickup.edit'));
        $this->seePageIs(route('pickups.edit', $pickup));

        $this->submitForm(trans('pickup.update'), [
            'courier_id'       => $pickupCourier->id,
            'delivery_unit_id' => $deliveryUnit->id,
            'customer_ids'     => $customers->pluck('id')->all(),
            'notes'            => 'Catatan pickup',
        ]);

        $this->seePageIs(route('pickups.show', $pickup));
        $this->see(trans('pickup.updated'));

        $customerIds = $customers->pluck('id')->toArray();

        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => null,
                'pcs_count'      => null,
                'items_count'    => null,
                'weight_total'   => null,
                'notes'          => null,
            ];
        }

        $this->seeInDatabase('pickups', [
            'courier_id' => $pickupCourier->id,
            'customers'  => json_encode($formattedCustomerIds),
            'notes'      => 'Catatan pickup',
        ]);
    }

    /** @test */
    public function user_can_delete_a_pickup()
    {
        $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->create();

        $this->visit(route('pickups.edit', $pickup));
        $this->click('del-pickup-'.$pickup->id);
        $this->seePageIs(route('pickups.edit', [$pickup->id, 'action' => 'delete']));

        $this->seeInDatabase('pickups', [
            'id' => $pickup->id,
        ]);

        $this->press(trans('app.delete_confirm_button'));

        $this->dontSeeInDatabase('pickups', [
            'id' => $pickup->id,
        ]);
    }
}
