<?php

namespace App\Policies;

use App\Entities\Customers\Customer;
use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isAccounting();
    }

    public function update(User $user, Customer $customer)
    {
        return $user->isAdmin() || $user->isAccounting();
    }
}
