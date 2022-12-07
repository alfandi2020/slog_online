<?php

namespace Tests\Unit\Models;

use App\Entities\Invoices\Cod as Invoice;
use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CodInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_delivered_cod_receipt_can_be_assigned_into_a_cod_invoice()
    {
        $receipt1 = factory(Receipt::class)->states('delivered_cod')->create();
        $receipt2 = factory(Receipt::class)->states('delivered_cod')->create();
        $invoice = factory(Invoice::class)->create();


        $invoice->assignReceipt($receipt1);
        $invoice->fresh()->assignReceipt($receipt2);

        $total = $receipt1->bill_amount + $receipt2->bill_amount;
        $this->assertEquals($total, $invoice->fresh()->amount);
    }

    /** @test */
    public function a_delivered_cod_receipt_can_be_removed_from_a_cod_invoice()
    {
        $receipt1 = factory(Receipt::class)->states('delivered_cod')->create();
        $receipt2 = factory(Receipt::class)->states('delivered_cod')->create();
        $invoice = factory(Invoice::class)->create();


        $invoice->assignReceipt($receipt1);
        $invoice->fresh()->assignReceipt($receipt2);

        $total = $receipt1->bill_amount + $receipt2->bill_amount;
        $this->assertEquals($total, $invoice->fresh()->amount);

        $invoice->fresh()->removeReceipt($receipt2);
        $this->assertEquals($receipt1->bill_amount, $invoice->fresh()->amount);

        $invoice->fresh()->removeReceipt($receipt1);
        $this->assertEquals(0, $invoice->fresh()->amount);
    }

    /** @test */
    public function a_other_than_cod_receipt_cannot_be_assigned_into_a_cod_invoice()
    {
        $receipt = factory(Receipt::class)->states(array_rand(['cash' => 'cash', 'cash' => 'cash']))->create();
        $invoice = factory(Invoice::class)->create();

        $result = $invoice->assignReceipt($receipt);

        $this->assertFalse($result);
        $this->assertNull($receipt->fresh()->invoice_id);
    }

    /** @test */
    public function a_cod_receipt_cannot_be_assigned_into_a_cod_invoice_if_it_doesnt_delivered()
    {
        $receipt = factory(Receipt::class)->states('cod')->create();
        $invoice = factory(Invoice::class)->create();

        $result = $invoice->assignReceipt($receipt);

        $this->assertFalse($result);
        $this->assertNull($receipt->invoice_id);
    }
}
