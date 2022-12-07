<?php

namespace Tests\Unit\Models;

use App\Entities\Customers\Customer;
use App\Entities\Invoices\Cash as CashInvoice;
use App\Entities\Invoices\Invoice;
use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Proof;
use App\Entities\Receipts\Receipt;
use App\Entities\Services\Rate;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_path_method()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertEquals(route('receipts.show', $receipt->number), $receipt->path());
    }

    /** @test */
    public function it_has_number_link_method()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertEquals(link_to_route('receipts.show', $receipt->number, [$receipt->number], [
            'title'  => 'Lihat detail Resi '.$receipt->number,
            'target' => '_blank',
        ]), $receipt->numberLink());
    }

    /** @test */
    public function it_has_progress_list_method()
    {
        $this->actingAs(User::find(4)); // Warehouse

        $receipt = factory(Receipt::class)->create();
        $handover = factory(Manifest::class, 'handover')->create();

        $result = $handover->addReceipt($receipt);
        $this->assertTrue($result, 'Resi tidak dapat ditambahkan ke Manifest Handover.');
        $handover->send();
        $handover->checkReceipt($receipt->number);
        $handover->receive();
        $this->assertTrue($handover->isReceived(), 'Manifest belum diterima.');
        $receipt = $receipt->fresh();
        $this->assertEquals('rw', $receipt->status_code);
        $this->assertCount(3, $receipt->progressList());
    }

    /** @test */
    public function it_has_get_invocie_number_method()
    {
        $receipt = factory(Receipt::class)->states('invoice_ready')->create();
        $invoice = factory(Invoice::class)->create();

        $invoice->assignReceipt($receipt);

        $this->assertTrue($receipt->invoice instanceof Invoice);
        $this->assertEquals($invoice->number, $receipt->getInvoiceNumber());

        $uninvoicedReceipt = factory(Receipt::class)->states('invoice_ready')->create();
        $this->assertEquals('-', $uninvoicedReceipt->getInvoiceNumber());
    }

    /** @test */
    public function it_has_get_invoice_link_method()
    {
        $receipt = factory(Receipt::class)->states('invoice_ready')->create();
        $invoice = factory(Invoice::class)->create();

        $invoice->assignReceipt($receipt);

        $this->assertTrue($receipt->invoice instanceof Invoice);
        $this->assertEquals(link_to_route('invoices.show', $invoice->number, [$invoice->id], ['target' => '_blank']), $receipt->getInvoiceLink());

        $uninvoicedReceipt = factory(Receipt::class)->states('invoice_ready')->create();
        $this->assertEquals('-', $uninvoicedReceipt->getInvoiceLink());
    }

    /** @test */
    public function it_has_payment_is_verified_method()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertFalse($receipt->fresh()->paymentIsClosed());

        $invoice = factory(CashInvoice::class)->create();
        $invoice->assignReceipt($receipt);

        $this->assertFalse($receipt->fresh()->paymentIsClosed());

        $invoice->verify();

        $this->assertTrue($receipt->fresh()->paymentIsClosed());
    }

    /** @test */
    public function it_has_payment_type_check_method()
    {
        $receipt = factory(Receipt::class)->make();
        $this->assertTrue($receipt->hasPaymentType(['cash', 'credit', 'cod']));

        $receipt = factory(Receipt::class)->make(['payment_type_id' => 1]);
        $this->assertFalse($receipt->hasPaymentType(['credit', 'cod']));

        $receipt = factory(Receipt::class)->make(['payment_type_id' => 2]);
        $this->assertFalse($receipt->hasPaymentType(['cash', 'cod']));

        $receipt = factory(Receipt::class)->make(['payment_type_id' => 3]);
        $this->assertFalse($receipt->hasPaymentType(['cash', 'credit']));
    }

    /** @test */
    public function it_has_courier_pickup_relation()
    {
        $pickupCourier = User::find(7); // Seeded courier_kalsel
        $receipt = factory(Receipt::class)->make(['pickup_courier_id' => $pickupCourier->id]);

        $this->assertInstanceOf(User::class, $receipt->pickupCourier);
        $this->assertEquals($pickupCourier->id, $receipt->pickupCourier->id);
    }

    /** @test */
    public function it_has_recalculate_bill_amount_method()
    {
        $receipt = factory(Receipt::class)->create();
        $rate = $receipt->rate;

        $this->assertEquals($rate->rate_kg, $receipt->amount);
        $this->assertEquals($rate->rate_kg, $receipt->bill_amount);
        $this->assertEquals($rate->rate_kg, $receipt->base_rate);

        $rate->rate_kg = 111111;
        $rate->save();

        $this->assertNotEquals($rate->rate_kg, $receipt->amount);
        $this->assertNotEquals($rate->rate_kg, $receipt->bill_amount);
        $this->assertNotEquals($rate->rate_kg, $receipt->base_rate);

        $receipt->recalculateBillAmount();

        $this->assertEquals($rate->rate_kg, $receipt->amount);
        $this->assertEquals($rate->rate_kg, $receipt->bill_amount);
        $this->assertEquals($rate->rate_kg, $receipt->base_rate);
    }

    /** @test */
    public function it_recalculate_bill_amount_correctly_when_charged_on_weight()
    {
        $weight = 3;
        $receipt = factory(Receipt::class)->create(['charged_on' => 1, 'weight' => $weight]);
        $rate = $receipt->rate;

        $billAmount = $rate->rate_kg;
        $this->assertEquals($billAmount, $receipt->amount);
        $this->assertEquals($billAmount, $receipt->bill_amount);
        $this->assertEquals($billAmount, $receipt->base_rate);
        $this->assertEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);

        $rate->rate_kg = 111111;
        $rate->save();

        $billAmount = $weight * $rate->rate_kg;
        $this->assertNotEquals($billAmount, $receipt->amount);
        $this->assertNotEquals($billAmount, $receipt->bill_amount);
        $this->assertNotEquals($rate->rate_kg, $receipt->base_rate);
        $this->assertNotEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);

        $receipt->recalculateBillAmount();

        $billAmount = $weight * $rate->rate_kg;
        $this->assertEquals($billAmount, $receipt->amount);
        $this->assertEquals($billAmount, $receipt->bill_amount);
        $this->assertEquals($rate->rate_kg, $receipt->base_rate);
        $this->assertEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);
    }

    /** @test */
    public function it_recalculate_bill_amount_correctly_when_charged_on_pcs()
    {
        $pcs = 3;
        $receipt = factory(Receipt::class, 'charge_on_pc')->create(['pcs_count' => $pcs]);
        $rate = $receipt->rate;

        $billAmount = $rate->rate_pc;
        $this->assertEquals($billAmount, $receipt->amount);
        $this->assertEquals($billAmount, $receipt->bill_amount);
        $this->assertEquals($billAmount, $receipt->base_rate);
        $this->assertEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);

        $rate->rate_pc = 111111;
        $rate->save();

        $billAmount = $pcs * $rate->rate_pc;
        $this->assertNotEquals($billAmount, $receipt->amount);
        $this->assertNotEquals($billAmount, $receipt->bill_amount);
        $this->assertNotEquals($rate->rate_pc, $receipt->base_rate);
        $this->assertNotEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);

        $receipt->recalculateBillAmount();

        $billAmount = $pcs * $rate->rate_pc;
        $this->assertEquals($billAmount, $receipt->amount);
        $this->assertEquals($billAmount, $receipt->fresh()->bill_amount);
        $this->assertEquals($rate->rate_pc, $receipt->base_rate);
        $this->assertEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);
    }

    /** @test */
    public function it_recalculate_bill_amount_correctly_and_change_rate_id_when_customer_rate_is_exist()
    {
        $pcs = 3;

        $receipt = factory(Receipt::class, 'charge_on_pc')->create([
            'pcs_count'   => $pcs,
            'customer_id' => factory(Customer::class)->create()->id,
        ]);

        $ratailRate = $receipt->rate;
        $customerRate = factory(Rate::class)->create([
            'rate_pc'          => '2000',
            'customer_id'      => $receipt->customer_id,
            'service_id'       => $receipt->service_id,
            'orig_city_id'     => $receipt->orig_city_id,
            'orig_district_id' => $receipt->orig_district_id,
            'dest_city_id'     => $receipt->dest_city_id,
            'dest_district_id' => $receipt->dest_district_id,
        ]);

        $receipt->recalculateBillAmount();

        $billAmount = $pcs * $customerRate->rate_pc;
        $this->assertEquals($billAmount, $receipt->amount);
        $this->assertEquals($billAmount, $receipt->fresh()->bill_amount);
        $this->assertEquals($customerRate->rate_pc, $receipt->base_rate);
        $this->assertEquals($receipt->rate_id, $customerRate->id);
        $this->assertEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);
    }

    /** @test */
    public function it_recalculate_bill_amount_correctly_and_apply_min_weight_on_customer_rate()
    {
        $weight = 3;
        $minWeight = 5;

        $receipt = factory(Receipt::class)->create([
            'weight'      => $weight,
            'customer_id' => factory(Customer::class)->create()->id,
        ]);

        $ratailRate = $receipt->rate;
        $customerRate = factory(Rate::class)->create([
            'rate_kg'          => 2000,
            'min_weight'       => $minWeight,
            'customer_id'      => $receipt->customer_id,
            'service_id'       => $receipt->service_id,
            'orig_city_id'     => $receipt->orig_city_id,
            'orig_district_id' => $receipt->orig_district_id,
            'dest_city_id'     => $receipt->dest_city_id,
            'dest_district_id' => $receipt->dest_district_id,
        ]);

        $receipt->recalculateBillAmount();

        $billAmount = $minWeight * $customerRate->rate_kg;
        $this->assertEquals($billAmount, $receipt->amount);
        $this->assertEquals($billAmount, $receipt->fresh()->bill_amount);
        $this->assertEquals($customerRate->rate_kg, $receipt->base_rate);
        $this->assertEquals($receipt->rate_id, $customerRate->id);
        $this->assertEquals([
            "base_charge"    => $billAmount,
            "discount"       => 0,
            "subtotal"       => $billAmount,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $billAmount,
        ], $receipt->costs_detail);
    }

    /** @test */
    public function a_receipt_has_has_one_proof_relation()
    {
        $receipt = factory(Receipt::class)->create();
        $proof = factory(Proof::class)->create(['receipt_id' => $receipt->id]);

        $this->assertInstanceOf(Proof::class, $receipt->proof);
        $this->assertEquals($proof->id, $receipt->proof->id);
    }
}
