<?php

namespace Feature\Invoices\Credit;

use App\Entities\Transactions\Transaction;
use App\Entities\Users\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class CreditPaymentsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function cashier_can_entry_invoice_income_payment()
    {
        $cashier = $this->loginAsCashier();
        $accounting = User::find(2);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, ['sent_date' => date('Y-m-d')]);
        $amount = $invoice->receipts->sum('bill_amount');

        $this->visit(route('invoices.show', $invoice));

        $this->submitForm(trans('invoice.payment_entry'), [
            'payment_date'      => date('Y-m-d'),
            'amount'            => $amount,
            'payment_method_id' => 2, // Seeded bank mandiri
            'in_out'            => 1, // 0: Pengeluaran, 2: Pemasukan
            'notes'             => 'Testing catatan transaksi',
        ]);

        $this->see(trans('invoice.payment_added', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice));

        $this->seeInDatabase('transactions', [
            'invoice_id'        => $invoice->id,
            'date'              => date('Y-m-d'),
            'in_out'            => 1, // 0: Pengeluaran, 2: Pemasukan
            'amount'            => $amount,
            'creator_id'        => $cashier->id,
            'handler_id'        => null,
            'verified_at'       => null,
            'payment_method_id' => 2, // Seeded bank mandiri
            'notes'             => 'Testing catatan transaksi',
        ]);
    }

    /** @test */
    public function cashier_can_entry_invoice_outcome_payment()
    {
        $cashier = $this->loginAsCashier();
        $accounting = User::find(2);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, ['sent_date' => date('Y-m-d')]);
        $amount = $invoice->receipts->sum('bill_amount');

        $this->visit(route('invoices.show', $invoice));

        $this->submitForm(trans('invoice.payment_entry'), [
            'payment_date'      => date('Y-m-d'),
            'amount'            => $amount,
            'payment_method_id' => 2, // Seeded bank mandiri
            'in_out'            => 0, // 0: Pengeluaran, 2: Pemasukan
            'notes'             => 'Testing catatan transaksi',
        ]);

        $this->see(trans('invoice.payment_added', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice));

        $this->seeInDatabase('transactions', [
            'invoice_id'        => $invoice->id,
            'date'              => date('Y-m-d'),
            'in_out'            => 0, // 0: Pengeluaran, 2: Pemasukan
            'amount'            => $amount,
            'creator_id'        => $cashier->id,
            'handler_id'        => null,
            'verified_at'       => null,
            'payment_method_id' => 2, // Seeded bank mandiri
            'notes'             => 'Testing catatan transaksi',
        ]);
    }

    /** @test */
    public function cashier_can_edit_invoice_payment()
    {
        $cashier = $this->loginAsCashier();
        $accounting = User::find(2);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, ['sent_date' => date('Y-m-d')]);
        $transaction = factory(Transaction::class)->create([
            'invoice_id' => $invoice->id,
            'creator_id' => $cashier->id,
            'amount'     => 100000,
            'in_out'     => 1,
        ]);

        $this->visit(route('invoices.show', $invoice));
        $this->click('edit_payment_'.$transaction->id);
        $this->seePageIs(route('invoices.show', [$invoice, 'action' => 'edit_payment', 'id' => $transaction->id]));

        $this->submitForm(trans('transaction.update'), [
            'date'              => Carbon::now()->format('Y-m-d'),
            'amount'            => 200000,
            'payment_method_id' => 2, // Seeded bank mandiri
            'in_out'            => 0, // 0: Pengeluaran, 2: Pemasukan
            'notes'             => 'Testing catatan transaksi',
        ]);

        $this->see(trans('invoice.payment_updated', ['number' => $transaction->number]));
        $this->seePageIs(route('invoices.show', $invoice));

        $this->seeInDatabase('transactions', [
            'invoice_id'        => $invoice->id,
            'date'              => Carbon::now()->format('Y-m-d'),
            'amount'            => 200000,
            'in_out'            => 0, // 0: Pengeluaran, 2: Pemasukan
            'creator_id'        => $cashier->id,
            'handler_id'        => null,
            'verified_at'       => null,
            'payment_method_id' => 2, // Seeded bank mandiri
            'notes'             => 'Testing catatan transaksi',
        ]);
    }
}
