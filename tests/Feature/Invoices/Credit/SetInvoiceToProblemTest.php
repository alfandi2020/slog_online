<?php

namespace Tests\Feature\Invoices\Credit;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SetInvoiceToProblemTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function accounting_can_set_sent_invoice_to_problem()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, [
            'sent_date' => date('Y-m-d'),
        ]);

        $this->visit(route('invoices.show', $invoice->id));

        $this->press(__('invoice.set_problem'));

        $this->see(__('invoice.problem', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertTrue($invoice->isProblem());
        $this->assertNotNull($invoice->problem_date);
    }

    /** @test */
    public function accounting_can_set_problem_invoice_back_to_sent()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, [
            'sent_date'    => date('Y-m-d'),
            'problem_date' => date('Y-m-d'),
        ]);

        $this->visit(route('invoices.show', $invoice->id));

        $this->press(__('invoice.unset_problem'));

        $invoice = $invoice->fresh();
        $this->see(__('invoice.'.$invoice->present()->status['code'], ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $this->assertTrue($invoice->isSent());
        $this->assertNull($invoice->problem_date);
    }
}
