<?php

namespace Tests\Feature\Invoices\Credit;

use App\Entities\Customers\Customer;
use App\Entities\Invoices\Invoice;
use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ManageCreditInvoicesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function accounting_can_see_customer_list()
    {
        $accounting = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accounting->network_id]);
        $receipt = factory(Receipt::class, 'customer')->states('invoice_ready')->create(['customer_id' => $customer->id]);
        $this->visit(route('invoices.customer-list'));
        $this->see($customer->account_no);
        $this->see($customer->name);
        $this->see(formatRp($receipt->bill_amount));
        $this->see('create-invoice-'.$customer->id);
    }

    /** @test */
    public function accounting_can_search_invoice_by_invoice_number()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);

        $this->visit(route('invoices.search'));
        $this->submitForm(trans('invoice.search'), [
            'q' => $invoice->number,
        ]);

        $this->seePageIs(route('invoices.search', ['q' => $invoice->number]));
        $this->see($invoice->number);
        $this->see($invoice->periode);
        $this->see($invoice->customer->name);
        $this->see(formatRp($invoice->receipts->sum('bill_amount')));
    }

    /** @test */
    public function accounting_can_see_invoice_list()
    {
        $accounting = $this->loginAsAccounting();
        $this->visit(route('invoices.index'));
    }

    /** @test */
    public function accounting_can_create_invoice_from_customer_list()
    {
        $accounting = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accounting->network_id]);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, ['number' => '6300'.date('ym').'0002']);
        $receipt = factory(Receipt::class)->states('invoice_ready')->create(['customer_id' => $customer->id]);

        $this->visit(route('invoices.customer-list'));
        $this->click('create-invoice-'.$customer->id);

        $this->type('2016-02', 'periode');
        $this->type('2016-02-01', 'date');
        $this->type('2016-03-01', 'end_date');
        $this->type('', 'discount');
        $this->type('', 'admin_fee');
        $this->type('', 'notes');
        $this->check('receipt_id['.$receipt->id.']');
        $this->press(trans('invoice.create'));

        $newNumber = '6300'.date('ym').'0003';

        $invoiceId = Invoice::where('number', $newNumber)->first()->id;
        // $this->see(trans('invoice.created'));

        $this->seeInDatabase('invoices', [
            'number'  => $newNumber,
            'type_id' => 2,
        ]);

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoiceId,
        ]);
    }

    /** @test */
    public function accounting_can_see_invoice_list_of_a_customer()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);

        $this->visit(route('customers.invoices', $invoice->customer_id));
        $this->see($invoice->number);
    }

    /** @test */
    public function accounting_can_make_invoice_from_customer_page()
    {
        $accounting = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accounting->network_id]);
        $receipts = factory(Receipt::class, 3)->states('invoice_ready')->create(['customer_id' => $customer->id]);
        $this->visit(route('customers.un-invoiced-receipts', $customer->id));
        $this->click(trans('invoice.create'));
        $this->seePageIs(route('invoices.create', $customer->id));
    }

    /** @test */
    public function accounting_can_edit_an_unsent_invoice_data()
    {
        $accounting = $this->loginAsAccounting();
        $otherAccountingUser = factory(User::class)->create(['role_id' => 2]);
        $invoice = $this->createInvoiceWithReceipts($accounting, 1);

        $this->visit(route('invoices.edit', $invoice->id));

        $this->submitForm(trans('invoice.update'), [
            'periode'    => '2016-02',
            'date'       => '2016-02-01',
            'end_date'   => '2016-03-01',
            'discount'   => '',
            'admin_fee'  => '',
            'creator_id' => $otherAccountingUser->id,
            'notes'      => '',
        ]);

        $this->see(trans('invoice.updated', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $this->seeInDatabase('invoices', [
            'id'         => $invoice->id,
            'periode'    => '2016-02',
            'date'       => '2016-02-01',
            'end_date'   => '2016-03-01',
            'notes'      => null,
            'amount'     => $invoice->receipts->sum('bill_amount'),
            'creator_id' => $otherAccountingUser->id,
        ]);
    }

    /** @test */
    public function invoice_amount_will_recalculated_if_charge_details_updated()
    {
        $discount = 1000;
        $adminFee = 2000;

        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 3);

        $this->visit(route('invoices.edit', $invoice->id));

        $this->submitForm(trans('invoice.update'), [
            'periode'   => '2016-02',
            'date'      => '2016-02-01',
            'end_date'  => '2016-03-01',
            'discount'  => $discount,
            'admin_fee' => $adminFee,
            'notes'     => '',
        ]);

        $this->see(trans('invoice.updated', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice->id));

        $amount = $invoice->receipts->sum('bill_amount') - $discount + $adminFee;

        $this->seeInDatabase('invoices', [
            'id'             => $invoice->id,
            'periode'        => '2016-02',
            'date'           => '2016-02-01',
            'end_date'       => '2016-03-01',
            'charge_details' => json_encode(['discount' => (string) $discount, 'admin_fee' => (string) $adminFee]),
            'notes'          => null,
            'amount'         => $amount,
        ]);
    }

    /** @test */
    public function accounting_can_delete_an_unsent_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 1);
        $receipt = $invoice->receipts->first();

        $this->seeInDatabase('receipts', ['id' => $receipt->id, 'invoice_id' => $invoice->id]);

        $this->visit(route('invoices.edit', $invoice->id));
        $this->click(trans('invoice.delete'));
        $this->seePageIs(route('invoices.delete', $invoice->id));
        $this->press(trans('invoice.delete'));

        $this->see(trans('invoice.deleted', ['number' => $invoice->number]));
        $this->seePageIs(route('customers.invoices', $invoice->customer_id));
        $this->dontSeeInDatabase('invoices', ['number' => $invoice->number]);
        $this->seeInDatabase('receipts', ['id' => $receipt->id, 'invoice_id' => null]);
    }

    /** @test */
    public function accounting_cannot_delete_an_sent_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 1, ['sent_date' => date('Y-m-d')]);

        $this->get(route('invoices.delete', $invoice->id));
        $this->seeStatusCode(403);
    }

    /** @test */
    public function accounting_can_add_customer_invoice_delivery_info()
    {
        $receivedDate = date('Y-m-d');
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 0, ['sent_date' => date('Y-m-d')]);
        $this->assertTrue($invoice->isSent());

        $this->visit(route('invoices.show', $invoice));
        $this->submitForm(trans('invoice.update_delivery'), [
            'consignor'     => 'Nama Pengirim',
            'consignee'     => 'Nama Penerima',
            'received_date' => $receivedDate,
        ]);

        $this->see(trans('invoice.delivery_updated', ['number' => $invoice->number]));
        $this->seePageIs(route('invoices.show', $invoice));

        $this->seeInDatabase('invoices', [
            'id'            => $invoice->id,
            'received_date' => $receivedDate,
            'delivery_info' => json_encode([
                'consignor' => 'Nama Pengirim',
                'consignee' => 'Nama Penerima',
            ]),
        ]);
    }
}
