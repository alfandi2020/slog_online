<?php

namespace App\Http\Controllers\Invoices;

use App\Entities\Invoices\Cod as Invoice;
use App\Entities\Transactions\PaymentMethod;
use App\Entities\Transactions\Transaction;
use App\Http\Controllers\Controller;
use Carbon;
use Illuminate\Http\Request;

class CodPaymentsController extends Controller
{
    public function index(Invoice $invoice)
    {
        $invoice->load('receipts.destination');
        $paymentMethods = PaymentMethod::pluck('name', 'id')->all();
        $editableTransaction = null;

        if (request('action') == 'edit_payment' && request('id')) {
            $editableTransaction = Transaction::find(request('id'));
        }

        return view('invoices.cod.payments', compact('invoice', 'paymentMethods', 'editableTransaction'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $yesterday = Carbon::createFromFormat('Y-m-d', $invoice->sent_date)->subDay()->format('Y-m-d');
        $this->validate($request, [
            'date'              => 'required|date_format:Y-m-d|after:'.$yesterday,
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required|numeric|exists:payment_methods,id',
            'notes'             => 'required|string|max:255',
        ]);

        \DB::beginTransaction();

        $transaction = new Transaction;
        $transaction->number = $transaction->generateNumber();
        $transaction->date = $request->get('date');
        $transaction->in_out = 0; // Pengeluaran
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
            'notes'             => 'required|string|max:255',
        ]);

        $transaction->update($transactionData);

        flash(trans('invoice.payment_updated', ['number' => $transaction->number]), 'success');
        return redirect()->route('invoices.cod.payments.index', $invoice);
    }
}
