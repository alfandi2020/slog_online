<?php

namespace Tests\Feature\Invoices\Cod;

use App\Entities\Invoices\Cod as CodInvoice;

class ManageCodInvoicesTest extends CodInvoicesTestCase
{
    /** @test */
    public function customer_service_can_see_cod_invoice_index_page()
    {
        $this->loginAsCustomerService();
        $codInvoice = factory(CodInvoice::class)->create();
        $this->visit(route('invoices.cod.index'));
        $this->see($codInvoice->number);
        $this->seeElement('a', ['href' => route('invoices.cod.create'), 'id' => 'create_cod_invoice']);
    }

    /** @test */
    public function customer_service_cannot_send_cod_invoice_with_no_receipts()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = factory(CodInvoice::class)->create();

        $this->visit(route('invoices.cod.show', $invoice->id));
        $this->assertTrue($invoice->isOnProccess());

        $this->doesntExpectEvents(\App\Events\Invoices\CodInvoiceSent::class);
        $this->dontSeeElement('input', ['value' => trans('cod_invoice.send')]);
    }

    /** @test */
    public function customer_service_can_send_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts();

        $this->visit(route('invoices.cod.show', $invoice->id));
        $this->assertTrue($invoice->isOnProccess());

        $this->expectsEvents(\App\Events\Invoices\CodInvoiceSent::class);
        $this->press(trans('cod_invoice.send'));

        $this->see(trans('cod_invoice.sent', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cod.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertTrue($invoice->isSent());
        $this->assertNotNull($invoice->sent_date);
    }

    /** @test */
    public function customer_service_can_take_cod_invoice_back()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts();
        $invoice->send();
        $this->assertTrue($invoice->isSent());

        $this->visit(route('invoices.cod.show', $invoice->id));

        $this->press(trans('cod_invoice.take_back'));

        $this->see(trans('cod_invoice.has_taken_back', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cod.show', $invoice->id));

        $invoice = $invoice->fresh();
        $this->assertFalse($invoice->isSent());
        $this->assertTrue($invoice->isOnProccess());
    }

    /** @test */
    public function customer_service_can_edit_an_unsent_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts(3);

        $this->visit(route('invoices.cod.edit', $invoice->id));

        $this->type('2016-02-01', 'date');
        $this->type('', 'notes');
        $this->press(trans('invoice.update'));

        $this->see(trans('cod_invoice.updated', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cod.show', $invoice->id));

        $this->seeInDatabase('invoices', [
            'id'          => $invoice->id,
            'type_id'     => 3,
            'periode'     => '2016-02-01',
            'date'        => '2016-02-01',
            'end_date'    => '2016-02-01',
            'notes'       => null,
            'customer_id' => null,
        ]);
    }

    /** @test */
    public function customer_service_cannot_edit_a_sent_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts();
        $invoice->send();
        $this->assertTrue($invoice->isSent());
        $this->visit(route('invoices.cod.edit', $invoice->id));
        $this->see(trans('invoice.uneditable'));
        $this->seePageIs(route('invoices.cod.show', $invoice->id));
        $this->dontSeeElement('a', ['href' => route('invoices.cod.edit', $invoice->id)]);
    }

    /** @test */
    public function customer_service_can_delete_an_unsent_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts();
        $receipt = $invoice->receipts->first();

        $this->seeInDatabase('receipts', ['id' => $receipt->id, 'invoice_id' => $invoice->id]);

        $this->visit(route('invoices.cod.edit', $invoice->id));
        $this->press(trans('invoice.delete'));

        $this->see(trans('invoice.deleted', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.cod.index'));
        $this->dontSeeInDatabase('invoices', ['number' => $invoice->number]);
        $this->seeInDatabase('receipts', ['id' => $receipt->id, 'invoice_id' => null]);
    }

    /** @test */
    public function customer_service_cannot_delete_an_sent_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts(1, ['sent_date' => date('Y-m-d')]);

        $this->delete(route('invoices.cod.destroy', $invoice->id));
        $this->seeStatusCode(403);
    }
}
