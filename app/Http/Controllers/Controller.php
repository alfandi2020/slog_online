<?php

namespace App\Http\Controllers;

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Users\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getDeliveryUnitsList($networkId)
    {
        $units = DeliveryUnit::where('network_id', $networkId)->where('is_active', 1)->get();
        $deliveryUnits = [];

        foreach ($units as $unit) {
            $deliveryUnits[$unit->id] = $unit->plat_no.' - '.$unit->name.' ('.$unit->type.')';
        }

        return $deliveryUnits;
    }

    protected function getPickupCouriersList($networkId)
    {
        return User::where('role_id', 7)
            ->where('network_id', $networkId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
