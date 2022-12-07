<?php

namespace App\Http\Controllers\Invoices;

use App\Entities\Invoices\Invoice;
use App\Http\Controllers\Controller;

class CreditReceiptsController extends Controller
{
    public function index(Invoice $invoice)
    {
        $invoice->load('receipts.destination');
        return view('invoices.credit.receipts', compact('invoice'));
    }
}
