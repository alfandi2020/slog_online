<?php

namespace App\Policies;

use App\Entities\Services\Rate;
use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatePolicy
{
    // use HandlesAuthorization;

    public function createReceipt(User $user, Rate $rate)
    {
        if (in_array($user->role_id, [1,3,4])
            && $user->network->origin_city_id == $rate->orig_city_id)
            return true;

        return false;
    }
}
