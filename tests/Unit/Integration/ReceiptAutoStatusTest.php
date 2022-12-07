<?php

namespace Tests\Unit\Integration;

use Tests\TestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReceiptAutoStatusTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function receipt_has_DE_status_on_created()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertEquals('de', $receipt->status_code);
    }

    /** @test */
    public function receipt_has_set_status_method()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertEquals('de', $receipt->status_code);

        $receipt = $receipt->setStatusCode('dl');
        $this->assertEquals('dl', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_MW_status_on_handover_manifest_send()
    {
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create();
        $manifest->addReceipt($receipt);

        $manifest = $manifest->fresh();
        $manifest->send();
        $this->assertCount(1, $manifest->receipts);

        $receipt = $receipt->fresh();
        $this->assertEquals('mw', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_DE_status_on_handover_manifest_taken_back()
    {
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create();
        $manifest->addReceipt($receipt);

        $manifest = $manifest->fresh();
        $manifest->send();
        $manifest->takeBack();

        $receipt = $receipt->fresh();
        $this->assertEquals('de', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_RW_status_on_handover_manifest_receipt_check()
    {
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create();
        $manifest->addReceipt($receipt);

        $manifest = $manifest->fresh();

        $manifest->checkReceipt($receipt->number);

        $receipt = $receipt->fresh();
        $this->assertEquals('rw', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_MN_status_on_delivery_manifest_send()
    {
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'delivery')->create();
        $manifest->addReceipt($receipt);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('mn', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_RW_status_on_delivery_manifest_taken_back()
    {
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'delivery')->create();
        $manifest->addReceipt($receipt);

        $manifest = $manifest->fresh();
        $manifest->send();
        $manifest->takeBack();

        $receipt = $receipt->fresh();
        $this->assertEquals('rw', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_RD_status_on_delivery_manifest_receipt_check()
    {
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'delivery')->create();
        $manifest->addReceipt($receipt);

        $manifest = $manifest->fresh();
        $this->assertCount(1, $manifest->receipts);

        $manifest->checkReceipt($receipt->number);

        $receipt = $receipt->fresh();
        $this->assertEquals('rd', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_OD_status_on_distribution_manifest_send()
    {
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest = factory(Manifest::class, 'distribution')->create(['dest_city_id' => '6301']);
        $manifest->addReceipt($receipt);

        $this->assertEquals('de', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('od', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_OD_status_on_distribution_manifest_take_back()
    {
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest = factory(Manifest::class, 'distribution')->create(['dest_city_id' => '6301']);
        $manifest->addReceipt($receipt);

        $this->assertEquals('de', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('od', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_OR_status_on_return_manifest_entry()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'dl', 'network_id' => 111]);
        $manifest = factory(Manifest::class, 'return')->create(['dest_network_id' => 111]);
        $manifest->addReceipt($receipt);

        $this->assertEquals('dl', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('or', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_RT_status_on_return_manifest_receipt_check()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'dl', 'network_id' => 111]);
        $manifest = factory(Manifest::class, 'return')->create(['dest_network_id' => 111]);
        $manifest->addReceipt($receipt);
        $this->assertEquals('dl', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('or', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->receive();

        $receipt = $receipt->fresh();
        $this->assertEquals('rt', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_MA_status_on_accounting_manifest_entry()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create();
        $manifest->addReceipt($receipt);

        $this->assertEquals('rt', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('ma', $receipt->status_code);
    }

    /** @test */
    public function receipt_get_IR_status_on_accounting_manifest_receipt_check()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create();
        $manifest->addReceipt($receipt);
        $this->assertEquals('rt', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertEquals('ma', $receipt->status_code);

        $manifest = $manifest->fresh();
        $manifest->checkReceipt($receipt->number);

        $receipt = $receipt->fresh();
        $this->assertEquals('ir', $receipt->status_code);
    }
}
