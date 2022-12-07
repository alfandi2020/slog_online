<?php

namespace Tests\Feature\Invoices\Cash;

use Tests\BrowserKitTestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Invoices\Cash as CashInvoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageCashInvoicesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sales_counter_can_see_cash_invoice_index_page()
    {
        $this->loginAsSalesCounter();
        $cashInvoice = factory(CashInvoice::class)->create();
        $this->visit(route('invoices.cash.index'));
        $this->see($cashInvoice->number);
        $this->seeElement('a', ['href' => route('invoices.cash.create'), 'id' => 'create_cash_invoice']);
    }

    /** @test */
    public function sales_counter_can_create_cash_invoice_for_cashier()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['creator_id' => $salesCounter->id]);
        $invoice = $this->createCashInvoiceWithReceipts(1, ['number' => 'CSH6300'.date('ym').'0001']);

        $this->visit(route('invoices.cash.index'));
        $this->seeElement('a', ['href' => route('invoices.cash.create'), 'id' => 'create_cash_invoice']);
        $this->click(trans('cash_invoice.create'));
        $this->seePageIs(route('invoices.cash.create'));

        $this->type('2016-02-01', 'date');
        $this->type('', 'notes');
        $this->check('receipt_id['.$receipt->id.']');
        $this->press(trans('cash_invoice.create'));

        $newNumber = 'CSH6300'.date('ym').'0002';

        $invoiceId = CashInvoice::where('number', $newNumber)->first()->id;
        $this->see(trans('cash_invoice.created'));

        $this->seeInDatabase('invoices', [
            'number'      => $newNumber,
            'type_id'     => 1,
            'customer_id' => null,
        ]);

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoiceId,
        ]);
    }

    /** @test */
    public function sales_counter_can_add_cash_receipt_to_cash_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['creator_id' => $salesCounter->id]);
        $invoice = $this->createCashInvoiceWithReceipts();

        $this->visit(route('invoices.cash.show', $invoice->id));
        $this->click(trans('manifest.add_remove_receipt'));
        $this->seePageIs(route('invoices.cash.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cash.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(2, $invoice->fresh()->receipts);
    }

    /** @test */
    public function sales_counter_cannot_add_credit_or_cod_receipt_to_cash_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create([
            'creator_id'      => $salesCounter->id,
            'payment_type_id' => rand(2, 3),
        ]);
        $invoice = $this->createCashInvoiceWithReceipts();

        $this->visit(route('invoices.cash.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cash.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->see(trans('cash_invoice.non_cash_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(1, $invoice->fresh()->receipts);
    }

    /** @test */
    public function sales_counter_can_remove_receipt_from_cash_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts();
        $receipt = $invoice->receipts->first();

        $this->visit(route('invoices.cash.show', [$invoice->id, 'action' => 'add_remove_receipt']));
        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cash.show', [$invoice->id, 'action' => 'add_remove_receipt']));
        $this->see(trans('manifest.receipt_removed'));
        $this->assertEmpty($invoice->fresh()->receipts);
    }

    /** @test */
    public function sales_counter_cannot_send_cod_invoice_with_no_receipts()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = factory(CashInvoice::class)->create();

        $this->visit(route('invoices.cash.show', $invoice->id));
        $this->assertTrue($invoice->isOnProccess());

        $this->doesntExpectEvents(\App\Events\Invoices\CashInvoiceSent::class);
        $this->dontSeeElement('input', ['value' => trans('cod_invoice.send')]);
    }

    /** @test */
    public function sales_counter_can_send_cash_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts();
        $this->assertTrue($invoice->isOnProccess());

        $this->visit(route('invoices.cash.show', $invoice->id));

        $this->expectsEvents(\App\Events\Invoices\CashInvoiceSent::class);
        $this->press(trans('cash_invoice.send'));

        $this->see(trans('cash_invoice.sent', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cash.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertTrue($invoice->isSent());
        $this->assertNotNull($invoice->sent_date);
    }

    /** @test */
    public function sales_counter_can_take_cash_invoice_back()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts();
        $invoice->send();
        $this->assertTrue($invoice->isSent());

        $this->visit(route('invoices.cash.show', $invoice->id));

        $this->press(trans('cash_invoice.take_back'));

        $this->see(trans('cash_invoice.has_taken_back', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cash.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertFalse($invoice->isSent());
        $this->assertTrue($invoice->isOnProccess());
    }

    /** @test */
    public function sales_counter_can_edit_an_unsent_cash_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts(3);

        $this->visit(route('invoices.cash.edit', $invoice->id));

        $this->type('2016-02-01', 'date');
        $this->type('', 'notes');
        $this->press(trans('invoice.update'));

        $this->see(trans('cash_invoice.updated', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cash.show', $invoice->id));

        $this->seeInDatabase('invoices', [
            'id'          => $invoice->id,
            'type_id'     => 1,
            'periode'     => '2016-02-01',
            'date'        => '2016-02-01',
            'end_date'    => '2016-02-01',
            'notes'       => null,
            'customer_id' => null,
        ]);
    }

    /** @test */
    public function sales_counter_cannot_edit_a_sent_cash_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts();
        $invoice->send();
        $this->assertTrue($invoice->isSent());
        $this->visit(route('invoices.cash.edit', $invoice->id));
        $this->see(trans('invoice.uneditable'));
        $this->seePageIs(route('invoices.cash.show', $invoice->id));
        $this->dontSeeElement('a', ['href' => route('invoices.cash.edit', $invoice->id)]);
    }

    /** @test */
    public function cashier_sales_counter_can_receive_and_verify_cash_invoice()
    {
        $invoice = $this->createCashInvoiceWithReceipts();
        $invoice->send();
        $this->assertFalse(
            $invoice->receipts->first()->paymentIsClosed(),
            'This Receipt Invoice has not been paid and verified.'
        );

        $cashier = $this->loginAsCashier();
        $this->visit(route('invoices.cash.show', $invoice->id));
        $this->seeElement('button', ['id' => 'verify_cash_invoice']);

        $this->expectsEvents(\App\Events\Invoices\CashInvoiceReceived::class);
        $this->press('verify_cash_invoice');

        $this->see(trans('cash_invoice.verified', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cash.show', $invoice->id));
        $this->seeInDatabase('invoices', [
            'id'           => $invoice->id,
            'type_id'      => 1,
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
    public function sales_counter_can_delete_an_unsent_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts();
        $receipt = $invoice->receipts->first();

        $this->seeInDatabase('receipts', ['id' => $receipt->id, 'invoice_id' => $invoice->id]);

        $this->visit(route('invoices.cash.edit', $invoice->id));
        $this->press(trans('invoice.delete'));

        $this->see(trans('invoice.deleted', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cash.index'));
        $this->dontSeeInDatabase('invoices', ['number' => $invoice->number]);
        $this->seeInDatabase('receipts', ['id' => $receipt->id, 'invoice_id' => null]);
    }

    /** @test */
    public function sales_counter_cannot_delete_an_sent_invoice()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $invoice = $this->createCashInvoiceWithReceipts(1, ['sent_date' => date('Y-m-d')]);

        $this->delete(route('invoices.cash.destroy', $invoice->id));
        $this->seeStatusCode(403);
    }

    protected function createCashInvoiceWithReceipts($numberOfReceipts = 1, $overrides = [])
    {
        $overrides = array_merge([
            'creator_id' => 3, // Seeded Sales Counter USer
            'network_id' => 1, // Seeded BAM Banjarmasin
        ], $overrides);

        $invoice = factory(CashInvoice::class)->create($overrides);

        $invoice->assignReceipt(
            factory(Receipt::class, $numberOfReceipts)->make()
        );

        return $invoice;
    }
}
