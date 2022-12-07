<?php

namespace App\Http\Controllers\Services\Pickups;

use App\Entities\Services\Pickup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceivedPickupsController extends Controller
{
    public function edit(Pickup $pickup)
    {
        $this->authorize('receive', $pickup);

        $networkId = auth()->user()->network_id;
        $deliveryUnits = $this->getDeliveryUnitsList($networkId);
        $pickupCouriers = $this->getPickupCouriersList($networkId);

        return view('services.pickups.receive', compact('pickup', 'deliveryUnits', 'pickupCouriers'));
    }

    public function update(Request $request, Pickup $pickup)
    {
        $this->authorize('receive', $pickup);

        $requestData = $request->validate([
            'sent_at'          => 'required|date|after_or_equal:today',
            'start_km'         => 'nullable|numeric',
            'returned_at'      => 'required|date|after_or_equal:'.$request->get('sent_at'),
            'end_km'           => 'nullable|numeric|min:'.$request->get('start_km'),
            'receipts_count.*' => 'required|numeric|min:0',
            'pcs_count.*'      => 'required|numeric|min:0',
            'items_count.*'    => 'required|numeric|min:0',
            'weight_total.*'   => 'required|numeric|min:0',
            'notes.*'          => 'nullable|string|max:255',
        ]);

        $customerPickups = $this->getCustomerPickupData($requestData);

        $pickup->customers = $customerPickups;
        $pickup->sent_at = $requestData['sent_at'].':00';
        $pickup->returned_at = $requestData['returned_at'].':00';
        $pickup->start_km = (int) $requestData['start_km'];
        $pickup->end_km = (int) $requestData['end_km'];
        $pickup->save();

        flash(trans('pickup.updated'), 'info');

        return redirect()->route('pickups.show', $pickup);
    }

    public function destroy(Pickup $pickup)
    {
        $pickup->returned_at = null;
        $pickup->save();

        flash(trans('pickup.return_canceled'), 'warning');

        return redirect()->route('pickups.receive', $pickup);
    }

    private function getCustomerPickupData(array $requestData)
    {
        $customerPickups = [];

        foreach ($requestData['receipts_count'] as $customerId => $receiptsCount) {
            $customerPickups[$customerId]['receipts_count'] = (int) $receiptsCount;
            $customerPickups[$customerId]['pcs_count'] = (int) $requestData['pcs_count'][$customerId];
            $customerPickups[$customerId]['items_count'] = (int) $requestData['items_count'][$customerId];
            $customerPickups[$customerId]['weight_total'] = (int) $requestData['weight_total'][$customerId];
            $customerPickups[$customerId]['notes'] = $requestData['notes'][$customerId];
        }

        return $customerPickups;
    }
}
