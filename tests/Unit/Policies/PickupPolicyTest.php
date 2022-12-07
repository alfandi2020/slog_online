<?php

namespace Tests\Unit\Policies;

use App\Entities\Services\Pickup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase as TestCase;

class PickupPolicyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_create_pickup()
    {
        $user = $this->loginAsWarehouse();

        $this->assertTrue($user->can('create', new Pickup));
    }

    /** @test */
    public function user_can_view_pickup()
    {
        $user = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->create();

        $this->assertTrue($user->can('view', $pickup));
    }

    /** @test */
    public function user_can_update_pickup()
    {
        $user = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->make();

        $this->assertTrue($user->can('update', $pickup));

        $pickup->sent_at = date('Y-m-d H:i:s');
        $this->assertFalse($user->can('update', $pickup));
    }

    /** @test */
    public function user_can_delete_pickup()
    {
        $user = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->create();

        $this->assertTrue($user->can('delete', $pickup));
    }

    /** @test */
    public function warehouse_can_send_pickup()
    {
        $warehouse = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->make();

        $this->assertTrue($warehouse->can('send', $pickup));
    }

    /** @test */
    public function warehouse_can_take_pickup_back()
    {
        $warehouse = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->make(['sent_at' => date('Y-m-d H:i:s')]);

        $this->assertTrue($warehouse->can('take-back', $pickup));
    }

    /** @test */
    public function warehouse_can_receive_pickup()
    {
        $warehouse = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->make();
        $pickup->sent_at = date('Y-m-d H:i:s');

        $this->assertTrue($warehouse->can('receive', $pickup));
    }

    /** @test */
    public function warehouse_can_cancel_returned_pickup()
    {
        $warehouse = $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->make();
        $pickup->sent_at = date('Y-m-d H:i:s');
        $pickup->returned_at = date('Y-m-d H:i:s');

        $this->assertTrue($warehouse->can('cancel-returned', $pickup));
    }
}
