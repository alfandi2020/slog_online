<?php

namespace App\Policies;

use App\Entities\Users\User;
use App\Entities\Invoices\Invoice;

class InvoicePolicy
{
    public function create(User $user, Invoice $invoice)
    {
        return $user->isAdmin() || $user->isAccounting() || $user->isBranchHead();
    }

    public function edit(User $user, Invoice $invoice)
    {
        return $invoice->isOnProccess()
            && ($user->isAdmin()
                || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id)
            );
    }

    public function send(User $user, Invoice $invoice)
    {
        return $this->edit($user, $invoice);
    }

    public function takeBack(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isPaid() == false
            && ($user->isAdmin() || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id));
    }

    public function updateDeliveryInfo(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isPaid() == false
            && ($user->isAdmin() || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id));
    }

    public function setProblem(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isPaid() == false && $invoice->isProblem() == false
            && ($user->isAdmin() || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id));
    }

    public function unsetProblem(User $user, Invoice $invoice)
    {
        return $invoice->isProblem()
            && ($user->isAdmin() || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id));
    }

    public function setPaid(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isPaid() == false
            && ($user->isAdmin() || ($user->isCashier() && $invoice->network_id == $user->network_id));
    }

    public function setUnpaid(User $user, Invoice $invoice)
    {
        return $invoice->isPaid() && $invoice->isVerified() == false
            && ($user->isAdmin() || ($user->isCashier() && $invoice->network_id == $user->network_id));
    }

    public function entryPayment(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isPaid() == false
            && ($user->isAdmin()
                || $user->isAccounting()
                || ($user->isCashier() || $user->isBranchHead() && $invoice->network_id == $user->network_id)
            );
    }

    public function verify(User $user, Invoice $invoice)
    {
        return $invoice->isPaid() && $invoice->isVerified() == false
            && ($user->isAdmin()
                || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id)
            );
    }

    public function delete(User $user, Invoice $invoice)
    {
        return $invoice->isOnProccess()
            && ($user->isAdmin()
                || ($user->isAccounting() || $user->isBranchHead() && $invoice->network_id == $user->network_id)
            );
    }

    public function addRemoveReceiptOf(User $user, Invoice $invoice)
    {
        return $this->edit($user, $invoice);
    }
}
