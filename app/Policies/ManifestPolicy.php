<?php

namespace App\Policies;

use App\Entities\Manifests\Manifest;
use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManifestPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService() || $user->isAccounting();
    }

    public function addRemoveReceiptOf(User $user, Manifest $manifest)
    {
        return $manifest->isNotSent()
            && ($user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService() || $user->isAccounting())
            && $user->id == $manifest->creator_id;
    }

    public function edit(User $user, Manifest $manifest)
    {
        return $manifest->isNotSent()
            && $manifest->isReceived() == false
            && $user->id == $manifest->creator_id;
    }

    public function print(User $user, Manifest $manifest)
    {
        return $manifest->receipts->count();
    }

    public function send(User $user, Manifest $manifest)
    {
        return $manifest->isNotSent()
            && ($user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService())
            && $manifest->isTypeOf('distribution') == false
            && $manifest->isReceived() == false
            && $user->id == $manifest->creator_id;
    }

    public function sendDistribution(User $user, Manifest $manifest)
    {
        return $manifest->isNotSent()
            && ($user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService())
            && $manifest->isTypeOf('distribution') == true
            && $manifest->isReceived() == false
            && $user->id == $manifest->creator_id;
    }

    public function takeBack(User $user, Manifest $manifest)
    {
        return $manifest->isSent()
            && ($user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService() || $user->isAccounting())
            && $manifest->isTypeOf('distribution') == false
            && $manifest->isReceived() == false
            && $user->id == $manifest->creator_id;
    }

    public function receive(User $user, Manifest $manifest)
    {
        return $manifest->isSent()
            && ($user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService() || $user->isAccounting())
            && $manifest->isTypeOf('distribution') == false
            && $manifest->isReceived() == false
            && $user->network_id == $manifest->dest_network_id;
    }

    public function receiveProblem(User $user, Manifest $manifest)
    {
        return $manifest->isSent()
            && $manifest->isTypeOf('problem')
            && $manifest->isReceived() == false
            && $user->id == $manifest->handler_id;
    }

    public function receiveDistribution(User $user, Manifest $manifest)
    {
        return $manifest->isSent()
            && ($user->isAdmin() || $user->isSalesCounter() || $user->isWarehouse() || $user->isCustomerService() || $user->isAccounting())
            && $manifest->isTypeOf('distribution') == true
            && $manifest->isReceived() == false
            && $user->network_id == $manifest->dest_network_id;
    }

    public function pod(User $user, Manifest $manifest)
    {
        return $manifest->isSent()
            && ($user->isAdmin() || $user->isCustomerService())
            && $manifest->isTypeOf('distribution')
            && $manifest->isReceived() == false
            && $user->network_id == $manifest->dest_network_id;
    }

    public function createProblemManifestOf(User $user, Manifest $manifest)
    {
        return $manifest->isReceived()
            && $user->id == $manifest->handler_id
            && $manifest->type_id != 6;
    }
}
