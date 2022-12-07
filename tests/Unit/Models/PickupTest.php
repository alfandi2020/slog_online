<?php

namespace Tests\Unit\Models;

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Networks\Network;
use App\Entities\Services\Pickup;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase as TestCase;

class PickupTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_number_link_method()
    {
        $pickup = factory(Pickup::class)->create();

        $this->assertEquals(
            link_to_route('pickups.show', $pickup->number, [$pickup->id], [
                'title' => trans(
                    'pickup.show_detail_title',
                    ['number' => $pickup->number]
                ),
            ]), $pickup->numberLink()
        );
    }

    /** @test */
    public function it_generates_its_own_number()
    {
        $pickup1 = factory(Pickup::class)->create();
        $this->assertEquals('PU'.date('ym').'0001', $pickup1->number);

        $pickup2 = factory(Pickup::class)->create();
        $this->assertEquals('PU'.date('ym').'0002', $pickup2->number);
    }

    /** @test */
    public function it_has_courier_relation()
    {
        $courier = User::find(7); // Seeded courier_kalsel
        $pickup = factory(Pickup::class)->make(['courier_id' => $courier->id]);

        $this->assertInstanceOf(User::class, $pickup->courier);
        $this->assertEquals($courier->id, $pickup->courier->id);
    }

    /** @test */
    public function it_has_status_attribute()
    {
        $pickup = factory(Pickup::class)->make();
        $this->assertEquals(trans('pickup.data_entry'), $pickup->status);

        $pickup->sent_at = date('Y-m-d H:i:s');
        $this->assertEquals(trans('pickup.on_pickup'), $pickup->status);

        $pickup->returned_at = date('Y-m-d H:i:s');
        $this->assertEquals(trans('pickup.returned'), $pickup->status);
    }

    /** @test */
    public function it_has_status_label_attribute()
    {
        $pickup = factory(Pickup::class)->make();
        $dataEntryStatus = '<span class="label label-default">'.trans('pickup.data_entry').'</span>';
        $this->assertEquals($dataEntryStatus, $pickup->status_label);

        $pickup->sent_at = date('Y-m-d H:i:s');
        $onPickupStatus = '<span class="label label-info">'.trans('pickup.on_pickup').'</span>';
        $this->assertEquals($onPickupStatus, $pickup->status_label);

        $pickup->returned_at = date('Y-m-d H:i:s');
        $returnedStatus = '<span class="label label-success">'.trans('pickup.returned').'</span>';
        $this->assertEquals($returnedStatus, $pickup->status_label);
    }

    /** @test */
    public function it_has_customers_count_attribute()
    {
        $customerIds = [1, 2, 3];
        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => 1,
                'pcs_count'      => 2,
                'notes'          => null,
            ];
        }

        $pickup = factory(Pickup::class)->make(['customers' => $formattedCustomerIds]);

        $this->assertEquals(3, $pickup->customers_count);
    }

    /** @test */
    public function it_has_receipts_count_attribute()
    {
        $customerIds = [1, 2, 3];
        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => 1,
                'pcs_count'      => 2,
                'items_count'    => 2,
                'weight_total'   => 10,
                'notes'          => null,
            ];
        }

        $pickup = factory(Pickup::class)->make(['customers' => $formattedCustomerIds]);

        $this->assertEquals(3, $pickup->receipts_count);
    }

    /** @test */
    public function it_has_pcs_count_attribute()
    {
        $customerIds = [1, 2, 3];
        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => 1,
                'pcs_count'      => 2,
                'items_count'    => 2,
                'weight_total'   => 10,
                'notes'          => null,
            ];
        }

        $pickup = factory(Pickup::class)->make(['customers' => $formattedCustomerIds]);

        $this->assertEquals(6, $pickup->pcs_count);
    }

    /** @test */
    public function it_has_items_count_attribute()
    {
        $customerIds = [1, 2, 3];
        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => 1,
                'pcs_count'      => 2,
                'items_count'    => 2,
                'weight_total'   => 10,
                'notes'          => null,
            ];
        }

        $pickup = factory(Pickup::class)->make(['customers' => $formattedCustomerIds]);

        $this->assertEquals(6, $pickup->items_count);
    }

    /** @test */
    public function it_has_weight_total_attribute()
    {
        $customerIds = [1, 2, 3];
        $formattedCustomerIds = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomerIds[$customerId] = [
                'receipts_count' => 1,
                'pcs_count'      => 2,
                'items_count'    => 2,
                'weight_total'   => 10,
                'notes'          => null,
            ];
        }

        $pickup = factory(Pickup::class)->make(['customers' => $formattedCustomerIds]);

        $this->assertEquals(30, $pickup->weight_total);
    }

    /** @test */
    public function it_has_belongs_to_creator_relation()
    {
        $pickup = factory(Pickup::class)->make();

        $this->assertInstanceOf(User::class, $pickup->creator);
        $this->assertEquals($pickup->creator_id, $pickup->creator->id);
    }

    /** @test */
    public function a_pickup_has_belongs_to_delivery_unit_relation()
    {
        $pickup = factory(Pickup::class)->make();

        $this->assertInstanceOf(DeliveryUnit::class, $pickup->deliveryUnit);
        $this->assertEquals($pickup->delivery_unit_id, $pickup->deliveryUnit->id);
    }

    /** @test */
    public function it_has_is_data_entry_method()
    {
        $pickup = factory(Pickup::class)->make();

        $this->assertTrue($pickup->isDataEntry());
    }

    /** @test */
    public function it_has_is_on_pickup_method()
    {
        $pickup = factory(Pickup::class)->make();
        $pickup->sent_at = date('Y-m-d H:i:s');

        $this->assertTrue($pickup->isOnPickup());
    }

    /** @test */
    public function it_has_has_returned_method()
    {
        $pickup = factory(Pickup::class)->make();
        $pickup->sent_at = date('Y-m-d H:i:s');
        $pickup->returned_at = date('Y-m-d H:i:s');

        $this->assertTrue($pickup->hasReturned());
    }

    /** @test */
    public function a_pickup_has_belongs_to_network_relation()
    {
        $pickup = factory(Pickup::class)->make();

        $this->assertInstanceOf(Network::class, $pickup->network);
        $this->assertEquals($pickup->network_id, $pickup->network->id);
    }
}
