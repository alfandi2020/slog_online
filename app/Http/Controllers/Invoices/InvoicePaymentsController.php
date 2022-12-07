<?php

namespace App\Http\Controllers\Invoices;

use App\Entities\Invoices\Invoice;
use App\Entities\Transactions\Transaction;
use App\Http\Controllers\Controller;
use Carbon;
use Illuminate\Http\Request;

class InvoicePaymentsController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $yesterday = Carbon::createFromFormat('Y-m-d', $invoice->sent_date)->subDay()->format('Y-m-d');
        $this->validate($request, [
            'payment_date'      => 'required|date_format:Y-m-d|after:'.$yesterday,
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required|numeric|exists:payment_methods,id',
            'in_out'            => 'required|numeric|in:0,1',
            'notes'             => 'required|string|max:255',
        ]);

        \DB::beginTransaction();

        $transaction = new Transaction;
        $transaction->number = $transaction->generateNumber();
        $transaction->date = $request->get('payment_date');
        $transaction->in_out = $request->get('in_out');
        $transaction->amount = $request->get('amount');
        $transaction->creator_id = auth()->id();
        $transaction->payment_method_id = $request->get('payment_method_id');
        $transaction->notes = $request->get('notes');
        $invoice->payments()->save($transaction);

        \DB::commit();

        flash(trans('invoice.payment_added', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function update(Invoice $invoice, Transaction $transaction)
    {
        $yesterday = Carbon::createFromFormat('Y-m-d', $invoice->sent_date)->subDay()->format('Y-m-d');

        $transactionData = request()->validate([
            'date'              => 'required|date_format:Y-m-d|after:'.$yesterday,
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required|numeric|exists:payment_methods,id',
            'in_out'            => 'required|numeric|in:0,1',
            'notes'             => 'required|string|max:255',
        ]);

        $transaction->update($transactionData);

        flash(trans('invoice.payment_updated', ['number' => $transaction->number]), 'success');
        return redirect()->route('invoices.show', $invoice);
    }
}
