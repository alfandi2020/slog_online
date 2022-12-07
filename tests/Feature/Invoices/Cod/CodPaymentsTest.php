<?php

namespace Tests\Feature\Invoices\Cod;

use App\Entities\Transactions\Transaction;

class CodPaymentsTest extends CodInvoicesTestCase
{
    /** @test */
    public function cashier_can_receive_and_verify_cod_invoice()
    {
        $invoice = $this->createCodInvoiceWithReceipts(1, ['sent_date' => date('Y-m-d')]);
        $this->assertFalse(
            $invoice->receipts->first()->paymentIsClosed(),
            'This Invoice has not been paid and verified.'
        );

        $cashier = $this->loginAsCashier();
        $this->visit(route('invoices.cod.show', $invoice->id));
        $this->seeElement('button', ['id' => 'verify_cod_invoice']);

        $this->expectsEvents(\App\Events\Invoices\CodInvoiceReceived::class);
        $this->press('verify_cod_invoice');

        $this->see(trans('cod_invoice.verified', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cod.show', $invoice->id));
        $this->seeInDatabase('invoices', [
            'id'           => $invoice->id,
            'type_id'      => 3,
            'payment_date' => date('Y-m-d'),
            'verify_date'  => date('Y-m-d'),
            'handler_id'   => $cashier->id,
            'customer_id'  => null,
        ]);
        $invoice = $invoice->fresh();
        $this->assertTrue(
            $invoice->receipts->first()->paymentIsClosed(),
            'This Receipt Invoice should now has paid and verified.'
        );

        $this->seeInDatabase('transactions', [
            'number'      => date('ym').'0001',
            'invoice_id'  => $invoice->id,
            'date'        => date('Y-m-d'),
            'in_out'      => 1,
            'amount'      => $invoice->receipts->sum('bill_amount'),
            'creator_id'  => $cashier->id,
            'handler_id'  => $cashier->id,
            'verified_at' => date('Y-m-d H:i:s'),
            'notes'       => null,
        ]);
    }

    /** @test */
    public function cashier_can_add_outcome_payment_on_cod_invoice()
    {
        $invoice = $this->createCodInvoiceWithReceipts(1, ['sent_date' => date('Y-m-d')]);

        $cashier = $this->loginAsCashier();

        $this->visit(route('invoices.cod.payments.index', $invoice->id));

        $outcomeAmount = 10000;
        $this->submitForm(trans('invoice.outcome_entry'), [
            'date'              => date('Y-m-d'),
            'amount'            => $outcomeAmount,
            'payment_method_id' => 1, // Seeded tunai
            'notes'             => 'Testing catatan transaksi',
        ]);

        $this->seePageIs(route('invoices.cod.payments.index', $invoice->id));

        $this->seeInDatabase('transactions', [
            'number'     => date('ym').'0001',
            'invoice_id' => $invoice->id,
            'date'       => date('Y-m-d'),
            'in_out'     => 0, // 0: Pengeluaran, 2: Pemasukan
            'amount'     => $outcomeAmount,
            'creator_id' => $cashier->id,
            // 'handler_id'  => $cashier->id,
            // 'verified_at' => date('Y-m-d H:i:s'),
            'notes'      => 'Testing catatan transaksi',
        ]);
    }

    /** @test */
    public function cashier_can_edit_payment_on_cod_invoice()
    {
        $cashier = $this->loginAsCashier();

        $invoice = $this->createCodInvoiceWithReceipts(1, ['sent_date' => date('Y-m-d')]);
        $transaction = factory(Transaction::class)->create([
            'invoice_id' => $invoice->id,
            'creator_id' => $cashier->id,
            'amount'     => $invoice->getAmount(),
            'in_out'     => 0, // Pengeluaran
        ]);

        $this->visit(route('invoices.cod.payments.index', $invoice->id));
        $this->click('edit_payment_'.$transaction->id);

        $outcomeAmount = 10000;
        $this->submitForm(trans('transaction.update'), [
            'date'              => date('Y-m-d'),
            'amount'            => $outcomeAmount,
            'payment_method_id' => 1, // Seeded tunai
            'notes'             => 'Testing catatan transaksi',
        ]);

        $this->seePageIs(route('invoices.cod.payments.index', $invoice->id));

        $this->seeInDatabase('transactions', [
            'invoice_id' => $invoice->id,
            'date'       => date('Y-m-d'),
            'in_out'     => 0, // 0: Pengeluaran, 2: Pemasukan
            'amount'     => $outcomeAmount,
            'creator_id' => $cashier->id,
            // 'handler_id'  => $cashier->id,
            // 'verified_at' => date('Y-m-d H:i:s'),
            'notes'      => 'Testing catatan transaksi',
        ]);
    }
}
