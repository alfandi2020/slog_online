<?php

namespace App\Events\Invoices;

use App\Entities\Invoices\Invoice;

class CodInvoiceSent
{
    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
