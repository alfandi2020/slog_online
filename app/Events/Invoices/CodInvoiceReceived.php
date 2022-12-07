<?php

namespace App\Events\Invoices;

use App\Entities\Invoices\Invoice;

class CodInvoiceReceived
{
    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
