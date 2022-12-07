<?php

namespace App\Notifications\Invoices;

use App\Entities\Invoices\Cash as CashInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CashInvoiceReceived extends Notification
{
    use Queueable;

    public $invoice;

    public function __construct(CashInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->invoice->number,
            'invoice_id' => $this->invoice->id,
            'receiver_id' => auth()->id(),
        ];
    }
}
