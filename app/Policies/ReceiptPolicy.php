<?php

namespace App\Policies;

use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceiptPolicy
{
    use HandlesAuthorization;

    function print(User $user, Receipt $receipt)
    {
        return true;
    }

    public function printItemsLabel(User $user, Receipt $receipt)
    {
        return !!$receipt->items_detail;
    }

    public function edit(User $user, Receipt $receipt)
    {
        return $receipt->hasStatusOf(['de'])
            && ($receipt->creator_id == $user->id || $user->isAdmin());
    }

    public function editPod(User $user, Receipt $receipt)
    {
        return $receipt->hasStatusOf(['dl', 'bd'])
            && ($user->isCustomerService() || $user->isAdmin())
            && ($receipt->pod->handler_id == $user->id || $user->isAdmin());
    }

    public function editCustomer(User $user, Receipt $receipt)
    {
        return $user->isAdmin() || $user->isAccounting() || $user->isBranchHead();
    }

    public function recalculateBillAmount(User $user, Receipt $receipt)
    {
        if ($receipt->payment_type_id != 2 || is_null($receipt->rate_id)) {
            return false;
        }

        return $user->isAdmin() || ($user->isAccounting() && $user->network_id == $receipt->network_id
        );
    }
}
