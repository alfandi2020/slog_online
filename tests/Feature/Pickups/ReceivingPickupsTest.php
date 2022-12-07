<?php

namespace Tests\Feature\Services;

use App\Entities\Customers\Customer;
use App\Entities\Services\Pickup;
use App\Entities\Users\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase as TestCase;

class ReceivingPickupsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function warehouse_can_receive_pickup_data_from_returned_pickup_courier()
    {
        $this->loginAsWarehouse();

        $formattedCustomerIds = $this->getFormattedCustomerData(
            factory(Customer::class, 3)->create()->pluck('id')->all()
        );

        $pickup = factory(Pickup::class)->create([
            'sent_at'   => Carbon::now(),
            'customers' => $formattedCustomerIds,
        ]);

        $pickupCourier = User::find(7); // Seeded Courier Kalsel

        $this->visit(route('pickups.show', $pickup));
        $this->click(trans('pickup.receive'));
        $this->seePageIs(route('pickups.receive', $pickup));

        $newPickupData = $this->getNewCustomerPickupData($pickup->customers);
        $newPickupData['sent_at'] = $pickup->sent_at->format('Y-m-d H:i');
        $newPickupData['returned_at'] = Carbon::now()->addHours(2)->format('Y-m-d H:i');
        $newPickupData['start_km'] = 100000;
        $newPickupData['end_km'] = 100080;

        $this->submitForm(trans('pickup.receive'), $newPickupData);

        $this->see(trans('pickup.updated'));
        $this->seePageIs(route('pickups.show', $pickup));

        $checkedCustomerPickups = $this->getCheckedCustomerPickupData($pickup->customers);

        $this->seeInDatabase('pickups', [
            'id'        => $pickup->id,
            'customers' => json_encode($checkedCustomerPickups),
            'start_km'  => 100000,
            'end_km'    => 100080,
        ]);
    }

    /** @test */
    public function warehouse_can_cancel_returned_pickup()
    {
        $this->loginAsWarehouse();

        $formattedCustomerIds = $this->getFormattedCustomerData(
            factory(Customer::class, 3)->create()->pluck('id')->all()
        );

        $sentTime = Carbon::now()->format('Y-m-d H:i:s');

        $pickup = factory(Pickup::class)->create([
            'sent_at'     => $sentTime,
            'returned_at' => Carbon::now(),
            'customers'   => $formattedCustomerIds,
        ]);

        $this->visit(route('pickups.show', $pickup));
        $this->press(trans('pickup.cancel_returned'));

        $this->see(trans('pickup.return_canceled'));
        $this->seePageIs(route('pickups.receive', $pickup));

        $this->seeInDatabase('pickups', [
            'id'          => $pickup->id,
            'sent_at'     => $sentTime,
            'returned_at' => null,
        ]);
    }

    private function getFormattedCustomerData(array $customerIds)
    {
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

        return $formattedCustomerIds;
    }

    private function getNewCustomerPickupData(array $customerPickups)
    {
        $newPickupData = [];

        foreach ($customerPickups as $customerId => $existingPickupData) {
            $newPickupData['receipts_count'][$customerId] = 2;
            $newPickupData['pcs_count'][$customerId] = 2;
            $newPickupData['items_count'][$customerId] = 2;
            $newPickupData['weight_total'][$customerId] = 10;
            $newPickupData['notes'][$customerId] = null;
        }

        return $newPickupData;
    }

    private function getCheckedCustomerPickupData(array $customerPickups)
    {
        $checkedCustomerPickups = [];

        foreach ($customerPickups as $customerId => $pickupData) {
            $checkedCustomerPickups[$customerId] = [
                'receipts_count' => 2,
                'pcs_count'      => 2,
                'items_count'    => 2,
                'weight_total'   => 10,
                'notes'          => null,
            ];
        }

        return $checkedCustomerPickups;
    }
}
