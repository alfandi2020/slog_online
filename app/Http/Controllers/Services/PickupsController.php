<?php

namespace App\Http\Controllers\Services;

use App\Entities\Customers\Customer;
use App\Entities\Services\Pickup;
use App\Entities\Users\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PickupsController extends Controller
{
    /**
     * Display a listing of the pickup.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pickups = Pickup::where(function ($query) {
            $query->where('number', 'like', '%'.request('q').'%');
            $user = auth()->user();
            if ($user->isAdmin() == false) {
                $query->where('network_id', $user->network_id);
            }
        })->paginate(25);

        return view('services.pickups.index', compact('pickups'));
    }

    /**
     * Display pickup create form.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $networkId = auth()->user()->network_id;
        $pickupCouriers = $this->getPickupCouriersList($networkId);
        $deliveryUnits = $this->getDeliveryUnitsList($networkId);
        $customers = Customer::where('network_id', $networkId)
            ->pluck('name', 'id');

        return view('services.pickups.create', compact('pickupCouriers', 'customers', 'deliveryUnits'));
    }

    /**
     * Store a newly created pickup in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newPickupData = $this->validate($request, [
            'courier_id'       => 'required|exists:users,id',
            'delivery_unit_id' => 'required|exists:delivery_units,id',
            'customer_ids'     => 'required|array',
            'notes'            => 'nullable|max:255',
        ]);

        $newPickupData['number'] = (new Pickup)->generateNumber();
        $newPickupData['creator_id'] = auth()->id();
        $newPickupData['customers'] = $this->getFormattedCustomerIds($newPickupData['customer_ids']);
        $newPickupData['network_id'] = auth()->user()->network_id;

        $pickup = Pickup::create($newPickupData);

        flash(trans('pickup.created'), 'success');

        return redirect()->route('pickups.show', $pickup);
    }

    /**
     * Display pickup detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Pickup $pickup)
    {
        $networkId = auth()->user()->network_id;
        $deliveryUnits = $this->getDeliveryUnitsList($networkId);
        $pickupCouriers = $this->getPickupCouriersList($networkId);

        return view('services.pickups.show', compact('pickup', 'deliveryUnits', 'pickupCouriers'));
    }

    /**
     * Display pickup edit form.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Pickup $pickup)
    {
        $this->authorize('update', $pickup);

        $networkId = auth()->user()->network_id;
        $pickupCouriers = $this->getPickupCouriersList($networkId);
        $deliveryUnits = $this->getDeliveryUnitsList($networkId);
        $customers = Customer::where('network_id', $networkId)
            ->pluck('name', 'id');

        return view('services.pickups.edit', compact('pickup', 'pickupCouriers', 'deliveryUnits', 'customers'));
    }

    /**
     * Update the specified pickup in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pickup $pickup)
    {
        $this->authorize('update', $pickup);

        $pickupData = $this->validate($request, [
            'courier_id'   => 'required|exists:users,id',
            'customer_ids' => 'required|array',
            'notes'        => 'nullable|max:255',
        ]);

        $pickupData['customers'] = $this->getFormattedCustomerIds($pickupData['customer_ids']);

        $pickup->update($pickupData);
        flash(trans('pickup.updated'), 'success');

        return redirect()->route('pickups.show', $pickup);
    }

    /**
     * Remove the specified pickup from storage.
     *
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pickup $pickup)
    {
        $this->validate(request(), [
            'pickup_id' => 'required',
        ]);

        $routeParam = request()->only('page', 'q');

        if (request('pickup_id') == $pickup->id && $pickup->delete()) {
            return redirect()->route('pickups.index', $routeParam);
        }

        return back();
    }

    public function send(Request $request, Pickup $pickup)
    {
        $this->authorize('send', $pickup);

        $pickupData = $request->validate([
            'sent_at'          => 'required|date',
            'start_km'         => 'nullable',
            'courier_id'       => 'required|exists:users,id',
            'delivery_unit_id' => 'required|exists:delivery_units,id',
            'notes'            => 'nullable|max:255',
        ]);

        $pickupData['sent_at'] .= ':00';

        $pickup->update($pickupData);

        flash(trans('pickup.sent'), 'success');

        return back();
    }

    public function takeBack(Request $request, Pickup $pickup)
    {
        $this->authorize('take-back', $pickup);

        $pickup->update(['sent_at' => null]);

        flash(trans('pickup.has_taken_back'), 'warning');

        return back();
    }

    private function getFormattedCustomerIds($customerIds)
    {
        $formattedCustomers = [];

        foreach ($customerIds as $customerId) {
            $formattedCustomers[$customerId] = [
                'receipts_count' => null,
                'pcs_count'      => null,
                'items_count'    => null,
                'weight_total'   => null,
                'notes'          => null,
            ];
        }

        return $formattedCustomers;
    }

    public function pdf(Pickup $pickup)
    {
        // return view('services.pickups.pdf', compact('pickup'));

        $pdf = \PDF::loadView('services.pickups.pdf', compact('pickup'));
        return $pdf->stream($pickup->number.'.e-pickup.pdf');
    }
}
