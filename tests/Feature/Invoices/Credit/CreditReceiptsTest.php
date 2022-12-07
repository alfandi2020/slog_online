<?php

namespace Tests\Feature\Invoices\Credit;

use App\Entities\Customers\Customer;
use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class CreditReceiptsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function accounting_can_see_un_invoiced_receipt_list_of_a_customer()
    {
        $accounting = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accounting->network_id]);
        $receipts = factory(Receipt::class, 3)->states('invoice_ready')->create(['customer_id' => $customer->id]);
        $this->visit(route('customers.un-invoiced-receipts', $customer->id));
        $this->see($receipts[0]->number);
        $this->see($receipts[1]->number);
        $this->see($receipts[2]->number);
    }

    /** @test */
    public function accounting_can_add_and_remove_receipt_from_an_invoice_edit_page()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 3);

        $this->visit(route('invoices.show', $invoice->id));

        $this->click(trans('invoice.edit'));

        $this->seePageIs(route('invoices.edit', $invoice->id));
        $receipt = $invoice->receipts->first();

        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_removed'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);
    }

    /** @test */
    public function accounting_can_add_and_remove_receipt_from_an_invoice_receipts_page()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting, 1);
        $receipt = $invoice->receipts->first();

        $this->visit(route('invoices.receipts.index', $invoice->id));
        $this->seePageIs(route('invoices.receipts.index', $invoice));

        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_removed'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_added'));
        $this->seePageIs(route('invoices.receipts.index', $invoice));

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);
    }

    /** @test */
    public function accounting_can_edit_receipt_costs_detail_of_an_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);
        $receipt = $invoice->receipts->first();

        $this->visit(route('invoices.edit', $invoice->id));
        $this->click('edit-receipt-cost-'.$receipt->id);
        $this->seePageIs(
            route('invoices.edit', [
                $invoice->id,
                'action'              => 'receipt_edit',
                'editable_receipt_id' => $receipt->id,
            ])
        );

        $this->see(trans('receipt.cost_edit_title', ['number' => $receipt->number, 'service' => $receipt->service()]));
        $this->submitForm(trans('receipt.cost_detail_update'), [
            'base_rate'           => '5000',
            'base_charge'         => '5000',
            'discount'            => '0',
            'packing_cost'        => '0',
            'insurance_cost'      => '1000',
            'add_cost'            => '0',
            'admin_fee'           => '2000',
            'weight'              => '3',
            'items_count'         => '3',
            'pcs_count'           => '1',
            'pcs_count'           => '1',
            'customer_invoice_no' => 'NOFAK,  TUR123,4567890.4567891', // Add space and remove space
            'notes'               => 'Catatan resi',
        ]);

        $this->see(trans('receipt.cost_detail_updated', ['number' => $receipt->number]));
        $this->seePageIs(route('invoices.edit', $invoice->id));

        $this->seeInDatabase('receipts', [
            'id'                  => $receipt->id,
            'weight'              => 3,
            'items_count'         => 3,
            'pcs_count'           => 1,
            'base_rate'           => 5000,
            'costs_detail'        => json_encode([
                'base_charge'    => 5000,
                'discount'       => 0,
                'subtotal'       => 5000,
                'packing_cost'   => 0,
                'insurance_cost' => 1000,
                'add_cost'       => 0,
                'admin_fee'      => 2000,
                'total'          => 8000,
            ]),
            'bill_amount'         => 8000,
            'customer_invoice_no' => 'NOFAK, TUR123, 4567890, 4567891', // returns proper comma and space separation
            'notes'               => 'Catatan resi',
        ]);

        $this->seeInDatabase('invoices', [
            'id'     => $invoice->id,
            'amount' => 8000,
        ]);
    }

    /** @test */
    public function accounting_can_recalculate_receipt_costs_detail_of_an_invoice_based_on_latest_rate()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);
        $receipt = $invoice->receipts->first();

        $rate = $receipt->rate;
        $rate->rate_kg = 111111;
        $rate->save();

        $this->assertEquals($rate->id, $receipt->rate_id);

        $this->visit(route('invoices.edit', $invoice->id));
        $this->click('edit-receipt-cost-'.$receipt->id);
        $this->seePageIs(
            route('invoices.edit', [
                $invoice->id,
                'action'              => 'receipt_edit',
                'editable_receipt_id' => $receipt->id,
            ])
        );

        $this->see(trans('receipt.cost_edit_title', ['number' => $receipt->number, 'service' => $receipt->service()]));

        $this->submitForm(trans('receipt.recalculate_bill_amount'));

        $this->see(trans('receipt.cost_detail_updated', ['number' => $receipt->number]));
        $this->seePageIs(route('invoices.edit', $invoice->id));

        $this->seeInDatabase('receipts', [
            'id'           => $receipt->id,
            'weight'       => 1,
            'items_count'  => 1,
            'pcs_count'    => 1,
            'base_rate'    => 111111,
            'costs_detail' => json_encode([
                'base_charge'    => 111111,
                'discount'       => 0,
                'subtotal'       => 111111,
                'packing_cost'   => 0,
                'insurance_cost' => 0,
                'add_cost'       => 0,
                'admin_fee'      => 0,
                'total'          => 111111,
            ]),
            'amount'       => 111111,
            'bill_amount'  => 111111,
        ]);
    }

    /** @test */
    public function accounting_can_recalculate_receipt_costs_detail_of_an_invoice_with_admin_free()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);
        $receipt = $invoice->receipts->first();

        $rate = $receipt->rate;
        $rate->rate_kg = 111111;
        $rate->save();

        $this->assertEquals($rate->id, $receipt->rate_id);

        $this->visit(route('invoices.edit', $invoice->id));
        $this->click('edit-receipt-cost-'.$receipt->id);
        $this->seePageIs(
            route('invoices.edit', [
                $invoice->id,
                'action'              => 'receipt_edit',
                'editable_receipt_id' => $receipt->id,
            ])
        );

        $this->see(trans('receipt.cost_edit_title', ['number' => $receipt->number, 'service' => $receipt->service()]));

        $this->submitForm(trans('receipt.recalculate_bill_amount'), [
            'admin_fee' => 1,
        ]);

        $this->see(trans('receipt.cost_detail_updated', ['number' => $receipt->number]));
        $this->seePageIs(route('invoices.edit', $invoice->id));

        $this->seeInDatabase('receipts', [
            'id'           => $receipt->id,
            'weight'       => 1,
            'items_count'  => 1,
            'pcs_count'    => 1,
            'base_rate'    => 111111,
            'costs_detail' => json_encode([
                'base_charge'    => 111111,
                'discount'       => 0,
                'subtotal'       => 111111,
                'packing_cost'   => 0,
                'insurance_cost' => 0,
                'add_cost'       => 0,
                'admin_fee'      => 1,
                'total'          => 111112,
            ]),
            'amount'       => 111112,
            'bill_amount'  => 111112,
        ]);
    }

    /** @test */
    public function accounting_can_add_credit_receipt_to_credit_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $receipt = factory(Receipt::class)->states('invoice_ready')->create(['creator_id' => $accounting->id]);
        $invoice = $this->createInvoiceWithReceipts($accounting);

        $this->visit(route('invoices.receipts.index', $invoice));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.receipts.index', $invoice));

        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(2, $invoice->fresh()->receipts);
    }

    /** @test */
    public function accounting_cannot_add_cash_or_cod_receipt_to_credit_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $receipt = factory(Receipt::class)->create([
            'payment_type_id' => array_rand([1 => 1, 3 => 3]),
        ]);
        $invoice = $this->createInvoiceWithReceipts($accounting);

        $this->visit(route('invoices.receipts.index', $invoice));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.receipts.index', $invoice));

        $this->see(trans('invoice.non_credit_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipts', [
            'id'         => $receipt->id,
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(1, $invoice->fresh()->receipts);
    }

    /** @test */
    public function accounting_can_remove_receipt_from_credit_invoice()
    {
        $accounting = $this->loginAsAccounting();
        $invoice = $this->createInvoiceWithReceipts($accounting);
        $receipt = $invoice->receipts->first();

        $this->visit(route('invoices.receipts.index', $invoice));
        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->seePageIs(route('invoices.receipts.index', $invoice));
        $this->see(trans('manifest.receipt_removed'));
        $this->assertEmpty($invoice->fresh()->receipts);
    }
}
