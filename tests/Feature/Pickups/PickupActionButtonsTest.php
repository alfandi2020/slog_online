<?php

namespace Tests\Feature\Services\Pickups;

use App\Entities\Services\Pickup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase as TestCase;

class PickupActionButtonsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function warehouse_can_send_pickup()
    {
        $this->loginAsWarehouse();
        $pickup = factory(Pickup::class)->create();
        $sentTime = date('Y-m-d H:i');

        $this->visit(route('pickups.show', $pickup));
        $this->submitForm(trans('pickup.send'), [
            'sent_at' => $sentTime,
            'notes'   => 'Catatan untuk driver pickup.',
        ]);

        $this->seePageIs(route('pickups.show', $pickup));
        $this->see(trans('pickup.sent'));

        $this->seeInDatabase('pickups', [
            'id'      => $pickup->id,
            'sent_at' => $sentTime.':00',
            'notes'   => 'Catatan untuk driver pickup.',
        ]);
    }

    /** @test */
    public function warehouse_takeback_a_sent_pickup()
    {
        $this->loginAsWarehouse();
        $sentTime = date('Y-m-d H:i:s');
        $pickup = factory(Pickup::class)->create(['sent_at' => $sentTime]);

        $this->assertTrue($pickup->isOnPickup());

        $this->visit(route('pickups.show', $pickup));
        $this->press(trans('pickup.take_back'));

        $this->seePageIs(route('pickups.show', $pickup));
        $this->see(trans('pickup.has_taken_back'));

        $this->seeInDatabase('pickups', [
            'id'      => $pickup->id,
            'sent_at' => null,
        ]);
    }

}
