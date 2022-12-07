<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use App\Entities\Users\User;
use App\Entities\Invoices\Invoice;
use App\Entities\Receipts\Receipt;
use App\Entities\Customers\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function an_invoice_has_recalculate_amount_method()
    {
        $discount = 1000;
        $adminFee = 2000;
        $invoice = factory(Invoice::class)->create([
            'charge_details' => [
                'discount'  => $discount,
                'admin_fee' => $adminFee,
            ],
        ]);
        $receipt = factory(Receipt::class)->states('invoice_ready')->create(['invoice_id' => $invoice->id]);

        $invoice->recalculateAmount();

        $amount = $receipt->bill_amount - $discount + $adminFee;
        $this->assertEquals($amount, $invoice->amount);
    }

    /** @test */
    public function a_receipt_can_be_assigned_into_an_invoice()
    {
        $receipt = factory(Receipt::class)->states('invoice_ready')->create();
        $invoice = factory(Invoice::class)->create();
        $this->assertEquals(0, $invoice->amount);

        $invoice->assignReceipt($receipt);

        $this->assertNotNull($receipt->invoice_id);
        $this->assertTrue($invoice->isOnProccess());
        $this->assertEquals($invoice->receipts->sum('bill_amount'), $invoice->amount);
    }

    /** @test */
    public function it_recalculates_correct_amount_on_receipt_assignment()
    {
        $discount = 1000;
        $adminFee = 2000;
        $invoice = factory(Invoice::class)->create([
            'charge_details' => [
                'discount'  => $discount,
                'admin_fee' => $adminFee,
            ],
        ]);
        $existingReceipt = factory(Receipt::class)->states('invoice_ready')->create(['invoice_id' => $invoice->id]);

        $receipt = factory(Receipt::class)->states('invoice_ready')->create();
        $invoice->assignReceipt($receipt);

        $amount = $existingReceipt->bill_amount + $receipt->bill_amount - $discount + $adminFee;
        $this->assertEquals($amount, $invoice->amount);
    }

    /** @test */
    public function a_receipt_can_be_removed_from_an_invoice()
    {
        $receipt = factory(Receipt::class)->states('invoice_ready')->create();
        $invoice = factory(Invoice::class)->create();

        $invoice->assignReceipt($receipt);

        $invoice = $invoice->fresh();
        $this->assertEquals($invoice->receipts()->sum('bill_amount'), $invoice->amount);

        $invoice->removeReceipt($receipt);

        $this->assertNull($receipt->invoice_id);
        $invoice = $invoice->fresh();
        $this->assertCount(0, $invoice->receipts);
        $this->assertEquals(0, $invoice->amount);
    }

    /** @test */
    public function it_recalculates_correct_amount_on_receipt_removal()
    {
        $discount = 1000;
        $adminFee = 2000;
        $invoice = factory(Invoice::class)->create([
            'charge_details' => [
                'discount'  => $discount,
                'admin_fee' => $adminFee,
            ],
        ]);
        $existingReceipt = factory(Receipt::class)->states('invoice_ready')->create(['invoice_id' => $invoice->id]);
        $receipt = factory(Receipt::class)->states('invoice_ready')->create(['invoice_id' => $invoice->id]);

        $invoice->removeReceipt($receipt);

        $amount = $existingReceipt->bill_amount - $discount + $adminFee;
        $this->assertEquals($amount, $invoice->amount);
    }

    /** @test */
    public function invoice_can_be_send()
    {
        $receipt = factory(Receipt::class)->states('invoice_ready')->create();
        $invoice = factory(Invoice::class)->create();

        $invoice->assignReceipt($receipt);

        $invoice->send();

        $this->assertNotNull($receipt->fresh()->invoice_id);
        $this->assertNotNull($invoice->sent_date);
        $this->assertTrue($invoice->isSent());
    }

    /** @test */
    public function invoice_cannot_be_send_if_has_no_receipts()
    {
        $invoice = factory(Invoice::class)->make();

        $invoice->send();

        $this->assertNull($invoice->sent_date);
    }

    /** @test */
    public function a_sent_invoice_can_be_taken_back()
    {
        $invoice = factory(Invoice::class)->states('sent')->make();

        $invoice->takeBack();

        $this->assertNull($invoice->sent_date);
    }

    /** @test */
    public function invoice_can_be_set_as_problem()
    {
        $invoice = factory(Invoice::class)->states('sent')->make();

        $invoice->setProblemDate(Carbon::now()->format('Y-m-d'));

        $this->assertTrue($invoice->isProblem());
    }

    /** @test */
    public function invoice_can_be_set_as_paid()
    {
        $invoice = factory(Invoice::class)->states('sent')->make();

        $invoice->setPaymentDate(Carbon::now()->format('Y-m-d'));

        $this->assertTrue($invoice->isPaid());
    }

    /** @test */
    public function invoice_can_be_set_as_unpaid()
    {
        $invoice = factory(Invoice::class)->states('paid')->make();

        $invoice->setPaymentDate(null);

        $this->assertFalse($invoice->isPaid());
    }

    /** @test */
    public function invoice_can_be_set_as_verified()
    {
        $invoice = factory(Invoice::class)->states('paid')->make();

        $invoice->verify();

        $this->assertTrue($invoice->isVerified());
    }

    /** @test */
    public function invoice_has_type()
    {
        $invoice = factory(Invoice::class)->make();
        $this->assertEquals(2, $invoice->type_id);
    }

    /** @test */
    public function invoice_has_add_payment_method()
    {
        $casier = \Auth::loginUsingId(6);

        $invoice = factory(Invoice::class)->create();
        $invoice->addPayment();

        $this->assertDatabaseHas('transactions', [
            'invoice_id'  => $invoice->id,
            'date'        => date('Y-m-d'),
            'in_out'      => 1,
            'amount'      => $invoice->receipts->sum('bill_amount'),
            'creator_id'  => $casier->id,
            'handler_id'  => $casier->id,
            'verified_at' => date('Y-m-d H:i:s'),
            'notes'       => null,
        ]);
    }

    /** @test */
    public function invoice_has_get_amount_method()
    {
        $discount = 1000;
        $adminFee = 2000;
        $invoice = factory(Invoice::class)->create([
            'charge_details' => [
                'discount'  => $discount,
                'admin_fee' => $adminFee,
            ],
        ]);
        $receipt = factory(Receipt::class)->states('invoice_ready')->create(['invoice_id' => $invoice->id]);

        $amount = $receipt->bill_amount - $discount + $adminFee;

        $this->assertEquals($amount, $invoice->getAmount());
    }

    /** @test */
    public function invoice_get_amount_method_has_correct_calculation_for_taxed_customer()
    {
        $discount = 1000;
        $adminFee = 2000;
        $customer = factory(Customer::class)->create(['is_taxed' => 1]);
        $invoice = factory(Invoice::class)->create([
            'charge_details' => [
                'discount'  => $discount,
                'admin_fee' => $adminFee,
            ],
            'customer_id'    => $customer->id,
        ]);
        $receipt = factory(Receipt::class)->states('invoice_ready')->create(['invoice_id' => $invoice->id]);

        $amount = $receipt->bill_amount - $discount + $adminFee;
        $taxAmount = 0.01 * $amount;
        $amount += $taxAmount;

        $this->assertEquals($amount, $invoice->getAmount());
    }

    /** @test */
    public function a_invoice_has_belongs_to_handler_relation()
    {
        $invoice = factory(Invoice::class)->make(['handler_id' => 2]);

        $this->assertInstanceOf(User::class, $invoice->handler);
        $this->assertEquals($invoice->handler_id, $invoice->handler->id);
    }
}
