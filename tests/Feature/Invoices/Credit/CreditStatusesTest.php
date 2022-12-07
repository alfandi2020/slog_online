<?php

namespace Tests\Feature\Invoices\Credit;

use App\Entities\Transactions\Transaction;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class CreditStatusesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function accounting_can_send_an_unsent_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);

        $this->visit(route('invoices.show', $invoice->id));

        $this->press(trans('invoice.send'));

        $this->see(trans('invoice.sent', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));
        $this->assertTrue($invoice->isOnProccess());

        $invoice = $invoice->fresh();
        $this->assertTrue($invoice->isSent());
        $this->assertNotNull($invoice->sent_date);
    }

    /** @test */
    public function accounting_can_take_back_a_sent_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);
        $invoice->send();
        $this->assertTrue($invoice->isSent());

        $this->visit(route('invoices.show', $invoice->id));

        $this->press(trans('invoice.take_back'));

        $this->see(trans('invoice.has_taken_back', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertFalse($invoice->isSent());
        $this->assertTrue($invoice->isOnProccess());
    }

    /** @test */
    public function cashier_can_set_invoice_as_paid_if_invoice_has_been_sent()
    {
        $cashier = $this->loginAsCashier();
        $accounting = User::find(2);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, [
            'sent_date' => date('Y-m-d'),
        ]);

        $this->visit(route('invoices.show', $invoice->id));

        $this->press(trans('invoice.set_paid'));

        $this->see(trans('invoice.paid', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertTrue($invoice->isPaid());
    }

    /** @test */
    public function cashier_can_set_invoice_as_unpaid_if_invoice_has_been_paid()
    {
        $cashier = $this->loginAsCashier();
        $accounting = User::find(2);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, [
            'sent_date'    => date('Y-m-d'),
            'payment_date' => date('Y-m-d'),
        ]);

        $this->visit(route('invoices.show', $invoice->id));

        $this->press(trans('invoice.set_unpaid'));

        $this->see(trans('invoice.unpaid', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertFalse($invoice->isPaid());
        $this->assertTrue($invoice->isSent());
    }

    /** @test */
    public function accounting_can_close_an_invoice_after_payment_entry()
    {
        $cashier = $this->loginAsCashier();
        $accounting = User::find(2);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, [
            'sent_date'    => date('Y-m-d'),
            'payment_date' => date('Y-m-d'),
        ]);
        $payments = factory(Transaction::class, 2)->create(['invoice_id' => $invoice->id]);

        $this->visit(route('invoices.show', $invoice->id));

        $this->actingAs($accounting);
        $this->visit(route('invoices.show', $invoice->id));
        $this->press(trans('invoice.verify'));
        $this->see(trans('invoice.verified', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $this->assertTrue($invoice->fresh()->isVerified());
        $this->seeInDatabase('invoices', [
            'id'          => $invoice->id,
            'verify_date' => date('Y-m-d'),
            'handler_id'  => $accounting->id,
        ]);
        $this->seeInDatabase('transactions', [
            'id'          => $payments[0]->id,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
        $this->seeInDatabase('transactions', [
            'id'          => $payments[1]->id,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
