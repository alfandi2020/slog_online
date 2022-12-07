<?php

namespace App\Listeners;

use App\Entities\Users\User;
use App\Events\Invoices\CashInvoiceReceived;
use App\Events\Invoices\CashInvoiceSent;
use App\Events\Invoices\CodInvoiceReceived;
use App\Events\Invoices\CodInvoiceSent;
use App\Notifications\Invoices\CashInvoiceSent as CashInvoiceSentNotif;
use App\Notifications\Invoices\CashInvoiceReceived as CashInvoiceReceivedNotif;
use App\Notifications\Invoices\CodInvoiceSent as CodInvoiceSentNotif;
use App\Notifications\Invoices\CodInvoiceReceived as CodInvoiceReceivedNotif;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Notification;

class InvoiceListener
{
    public function cashInvoiceSent(CashInvoiceSent $event)
    {
        $invoice = $event->invoice;
        $users = User::where(function($query) use ($invoice) {
            $query->where('network_id', $invoice->network_id);
            $query->whereIn('role_id', [6]); // Cashier
        })
        ->get();

        Notification::send($users, new CashInvoiceSentNotif($invoice));
    }

    public function cashInvoiceReceived(CashInvoiceReceived $event)
    {
        $invoice = $event->invoice;
        $user = $invoice->creator;

        $user->notify(new CashInvoiceReceivedNotif($invoice));
    }

    public function codInvoiceSent(CodInvoiceSent $event)
    {
        $invoice = $event->invoice;
        $users = User::where(function($query) use ($invoice) {
            $query->where('network_id', $invoice->network_id);
            $query->whereIn('role_id', [6]); // Cashier
        })
        ->get();

        Notification::send($users, new CodInvoiceSentNotif($invoice));
    }

    public function codInvoiceReceived(CodInvoiceReceived $event)
    {
        $invoice = $event->invoice;
        $user = $invoice->creator;

        $user->notify(new CodInvoiceReceivedNotif($invoice));
    }
}
