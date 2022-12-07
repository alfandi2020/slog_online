<?php

namespace App\Policies;

use App\Entities\Networks\Network;
use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NetworkPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Network $network)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Network $network)
    {
        return $user->isAdmin()
            && $network->users()->count() == 0
            && $network->customers()->count() == 0
            && $network->receipts()->count() == 0
            && $network->deliveryUnits()->count() == 0;
    }
}
