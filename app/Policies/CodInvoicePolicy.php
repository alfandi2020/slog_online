<?php

namespace App\Policies;

use App\Entities\Invoices\Cod as Invoice;
use App\Entities\Users\User;

class CodInvoicePolicy
{
    public function create(User $user, Invoice $invoice)
    {
        return $user->isAdmin() || $user->isCustomerService();
    }

    public function edit(User $user, Invoice $invoice)
    {
        return $invoice->isOnProccess()
            && ($user->isAdmin() || ($user->isCustomerService() && $invoice->creator_id == $user->id));
    }

    public function verify(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isVerified() == false
            && ($user->isAdmin() || ($user->isCashier() && $invoice->network_id == $user->network_id));
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
        return $invoice->isSent() && $invoice->isPaid() == false && $invoice->creator_id == $user->id;
    }

    public function delete(User $user, Invoice $invoice)
    {
        return $this->edit($user, $invoice);
    }

    public function entryPayment(User $user, Invoice $invoice)
    {
        return $invoice->isSent() && $invoice->isPaid() == false
            && ($user->isAdmin() || ($user->isCashier() && $invoice->network_id == $user->network_id));
    }
}
