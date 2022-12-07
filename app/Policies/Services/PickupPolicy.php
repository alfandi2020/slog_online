<?php

namespace App\Policies\Services;

use App\Entities\Services\Pickup;
use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PickupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function view(User $user, Pickup $pickup)
    {
        // Update $user authorization to view $pickup here.
        return true;
    }

    /**
     * Determine whether the user can create pickups.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function create(User $user, Pickup $pickup)
    {
        // Update $user authorization to create $pickup here.
        return $user->isWarehouse() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the pickup.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function update(User $user, Pickup $pickup)
    {
        // Update $user authorization to update $pickup here.
        return $pickup->isDataEntry()
            && (
            $user->isAdmin() ||
            ($user->isWarehouse() && $pickup->network_id)
        );
    }

    /**
     * Determine whether the user can delete the pickup.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function delete(User $user, Pickup $pickup)
    {
        // Update $user authorization to delete $pickup here.
        return $user->isAdmin() || (
            $user->isWarehouse() && $pickup->network_id
        );
    }

    /**
     * Determine whether the user can send pickups.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function send(User $user, Pickup $pickup)
    {
        // Update $user authorization to send $pickup here.
        // @TODO Warehouse user must on same network_id with pickup
        return ($user->isWarehouse() || $user->isAdmin()) && $pickup->isDataEntry();
    }

    /**
     * Determine whether the user can take pickups back.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function takeBack(User $user, Pickup $pickup)
    {
        // Update $user authorization to take $pickup back here.
        // @TODO Warehouse user must on same network_id with pickup
        return ($user->isWarehouse() || $user->isAdmin())
        && $pickup->isOnPickup()
        && !$pickup->hasReturned();
    }

    /**
     * Determine whether the user can receive pickups.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function receive(User $user, Pickup $pickup)
    {
        // Update $user authorization to receive $pickup here.
        // @TODO Warehouse user must on same network_id with pickup
        return ($user->isWarehouse() || $user->isAdmin()) && $pickup->isOnPickup();
    }

    /**
     * Determine whether the user can cancel returned pickups.
     *
     * @param  \App\Entities\Users\User  $user
     * @param  \App\Entities\Services\Pickup  $pickup
     * @return boolean
     */
    public function cancelReturned(User $user, Pickup $pickup)
    {
        // Update $user authorization to receive $pickup here.
        return ($user->isWarehouse() || $user->isAdmin()) && $pickup->hasReturned();
    }
}
