<?php

namespace Tests\Feature\Invoices\Cod;

use App\Entities\Invoices\Cod as CodInvoice;
use App\Entities\Receipts\Receipt;

class ManageReceiptsTest extends CodInvoicesTestCase
{
    /** @test */
    public function customer_service_can_create_cod_invoice_for_cashier()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts(1, ['number' => 'COD6300'.date('ym').'0001']);

        $this->visit(route('invoices.cod.index'));
        $this->seeElement('a', ['href' => route('invoices.cod.create'), 'id' => 'create_cod_invoice']);
        $this->click(trans('cod_invoice.create'));
        $this->seePageIs(route('invoices.cod.create'));

        $this->type('2016-02-01', 'date');
        $this->type('', 'notes');
        $this->press(trans('cod_invoice.create'));

        $newNumber = 'COD6300'.date('ym').'0002';

        $invoiceId = CodInvoice::where('number', $newNumber)->first()->id;

        $this->seePageIs(route('invoices.cod.show', [$invoiceId, 'action' => 'add_remove_receipt']));
        $this->see(trans('cod_invoice.created'));

        $this->seeInDatabase('invoices', [
            'number'      => $newNumber,
            'type_id'     => 3,
            'customer_id' => null,
        ]);
    }

    /** @test */
    public function customer_service_can_add_cod_receipt_to_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->states('delivered_cod')->create([
            'creator_id'  => $customerService->id,
            'status_code' => 'rt',
        ]);
        $invoice = $this->createCodInvoiceWithReceipts();

        $this->visit(route('invoices.cod.show', $invoice->id));
        $this->click(trans('manifest.add_remove_receipt'));
        $this->seePageIs(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(2, $invoice->fresh()->receipts);
    }

    /** @test */
    public function customer_service_cannot_add_undelivered_receipt_to_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->states('cod')->create([
            'creator_id' => $customerService->id,
        ]);
        $invoice = $this->createCodInvoiceWithReceipts();

        $this->visit(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->see(trans('manifest.receipt_addition_fails'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(1, $invoice->fresh()->receipts);
    }

    /** @test */
    public function customer_service_cannot_add_credit_or_cod_receipt_to_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->create([
            'creator_id'      => $customerService->id,
            'payment_type_id' => rand(1, 2),
        ]);
        $invoice = $this->createCodInvoiceWithReceipts();

        $this->visit(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->see(trans('cod_invoice.non_cod_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(1, $invoice->fresh()->receipts);
    }

    /** @test */
    public function dl_or_bd_receipt_must_be_returned_before_add_to_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->states('delivered_cod')->create([
            'creator_id' => $customerService->id,
        ]);

        $invoice = $this->createCodInvoiceWithReceipts();

        $this->visit(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));

        $this->see(trans('cod_invoice.dl_bd_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(1, $invoice->fresh()->receipts);
    }

    /** @test */
    public function customer_service_can_remove_receipt_from_cod_invoice()
    {
        $customerService = $this->loginAsCustomerService();
        $invoice = $this->createCodInvoiceWithReceipts();
        $receipt = $invoice->receipts->first();

        $this->visit(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));
        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']));
        $this->see(trans('manifest.receipt_removed'));
        $this->assertEmpty($invoice->fresh()->receipts);
    }
}
