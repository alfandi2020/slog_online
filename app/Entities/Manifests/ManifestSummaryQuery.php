<?php

namespace App\Entities\Manifests;

use App\Entities\Manifests\Manifest;
use App\Entities\Users\User;

/**
* Manifest Summary Query Class
*/
class ManifestSummaryQuery
{
    private $manifest;
    private $user;

    public function __construct(User $user = null)
    {
        $this->manifest = new Manifest;
        $this->user = is_null($user) ? $user : auth()->user();
    }

    public function handover()
    {
        return $this->manifest
            ->where('orig_network_id', $this->user->network_id)
            ->where('type_id', 1)
            ->whereNull('handler_id')
            ->whereNull('received_at')
            ->count();
    }

    public function deliveryOut()
    {
        return $this->manifest
            ->where('orig_network_id', $this->user->network_id)
            ->where('type_id', 2)
            ->whereNull('handler_id')
            ->whereNull('received_at')
            ->count();
    }

    public function deliveryIn()
    {
        return $this->manifest
            ->where('dest_network_id', $this->user->network_id)
            ->where('type_id', 2)
            ->whereNull('handler_id')
            ->whereNull('received_at')
            ->count();
    }

    public function distribution()
    {
        return $this->manifest
            ->where('orig_network_id', $this->user->network_id)
            ->where('type_id', 3)
            ->whereNull('received_at')
            ->count();
    }

    public function returnOut()
    {
        return $this->manifest
            ->where('orig_network_id', $this->user->network_id)
            ->where('type_id', 4)
            ->whereNull('handler_id')
            ->whereNull('received_at')
            ->count();
    }

    public function returnIn()
    {
        return $this->manifest
            ->where('dest_network_id', $this->user->network_id)
            ->where('type_id', 4)
            ->whereNull('handler_id')
            ->whereNull('received_at')
            ->count();
    }

    public function accounting()
    {
        return $this->manifest
            ->where('orig_network_id', $this->user->network_id)
            ->where('dest_network_id', $this->user->network_id)
            ->where('type_id', 5)
            ->whereNull('handler_id')
            ->whereNull('received_at')
            ->count();
    }

    public function problem()
    {
        return $this->manifest
            ->where('orig_network_id', $this->user->network_id)
            ->where('dest_network_id', $this->user->network_id)
            ->where('type_id', 6)
            ->where('handler_id', $this->user->id)
            ->whereNotNull('deliver_at')
            ->whereNull('received_at')
            ->count();
    }
}