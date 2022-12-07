<?php

namespace App\Policies;

use App\Entities\Invoices\Cash as Invoice;
use App\Entities\Users\User;

class CashInvoicePolicy
{
    public function create(User $user, Invoice $invoice)
    {
        return $user->isAdmin() || $user->isSalesCounter() || $user->isBranchHead();
    }

    public function edit(User $user, Invoice $invoice)
    {
        return $invoice->isOnProccess()
            && (
                $user->isAdmin()
                || ($user->isSalesCounter() || $user->isBranchHead() && $invoice->creator_id == $user->id)
            );
    }

    public function verify(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isVerified() == false
            && (
                $user->isAdmin()
                || ($user->isCashier() || $user->isBranchHead() && $invoice->network_id == $user->network_id)
            );
    }

    public function addRemoveReceiptOf(User $user, Invoice $invoice)
    {
        return $this->edit($user, $invoice);
    }

    public function send(User $user, Invoice $invoice)
    {
        return $this->edit($user, $invoice) && $invoice->receipts()->count();
    }

    public function undeliver(User $user, Invoice $invoice)
    {
        return $invoice->isSent()
            && $invoice->isPaid() == false
            && $invoice->creator_id == $user->id;
    }

    public function delete(User $user, Invoice $invoice)
    {
        return $this->edit($user, $invoice);
    }
}
