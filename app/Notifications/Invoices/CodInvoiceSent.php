<?php

namespace App\Notifications\Invoices;

use App\Entities\Invoices\Cod as CodInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CodInvoiceSent extends Notification
{
    use Queueable;

    public $invoice;

    public function __construct(CodInvoice $invoice)
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
            'sender_id' => $this->invoice->creator_id,
        ];
    }
}
