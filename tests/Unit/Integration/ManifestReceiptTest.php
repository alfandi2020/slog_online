<?php

namespace Tests\Unit\Integration;

use Tests\TestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManifestReceiptTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function only_de_receipt_can_added_to_handover_manifest()
    {
        $deReceipt = factory(Receipt::class)->create(['status_code' => 'de']);
        $rwReceipt = factory(Receipt::class)->create(['status_code' => 'rw']);
        $pdReceipt = factory(Receipt::class)->create(['status_code' => 'pd']);
        $handoverManifest = factory(Manifest::class, 'handover')->create();
        $handoverManifest->addReceipt($deReceipt);
        $handoverManifest->addReceipt($rwReceipt);
        $handoverManifest->addReceipt($pdReceipt);

        $this->assertCount(2, $handoverManifest->fresh()->receipts);
    }

    /** @test */
    public function de_rw_rd_receipt_can_added_to_distribution_manifest()
    {
        $deReceipt = factory(Receipt::class)->create(['status_code' => 'de', 'dest_city_id' => '6301']);
        $rwReceipt = factory(Receipt::class)->create(['status_code' => 'rw', 'dest_city_id' => '6301']);
        $rdReceipt = factory(Receipt::class)->create(['status_code' => 'rd', 'dest_city_id' => '6301']);
        $pdReceipt = factory(Receipt::class)->create(['status_code' => 'pd', 'dest_city_id' => '6301']);
        $distributionManifest = factory(Manifest::class, 'distribution')->create(['dest_city_id' => '6301']);
        $distributionManifest->addReceipt($deReceipt);
        $distributionManifest->addReceipt($rwReceipt);
        $distributionManifest->addReceipt($rdReceipt);
        $distributionManifest->addReceipt($pdReceipt);

        $this->assertCount(4, $distributionManifest->fresh()->receipts);
    }

    /** @test */
    public function non_delivered_receipts_receipt_can_added_to_distribution_manifest()
    {
        $auReceipt = factory(Receipt::class)->create(['status_code' => 'au', 'dest_city_id' => '6301']);
        $mrReceipt = factory(Receipt::class)->create(['status_code' => 'mr', 'dest_city_id' => '6301']);
        $o1Receipt = factory(Receipt::class)->create(['status_code' => 'o1', 'dest_city_id' => '6301']);
        // $o2Receipt = factory(Receipt::class)->create(['status_code' => 'o2', 'dest_city_id' => '6301']);
        // $o3Receipt = factory(Receipt::class)->create(['status_code' => 'o3', 'dest_city_id' => '6301']);
        // $o4Receipt = factory(Receipt::class)->create(['status_code' => 'o4', 'dest_city_id' => '6301']);
        // $o5Receipt = factory(Receipt::class)->create(['status_code' => 'o5', 'dest_city_id' => '6301']);
        // $o6Receipt = factory(Receipt::class)->create(['status_code' => 'o6', 'dest_city_id' => '6301']);
        // $o7Receipt = factory(Receipt::class)->create(['status_code' => 'o7', 'dest_city_id' => '6301']);
        // $o8Receipt = factory(Receipt::class)->create(['status_code' => 'o8', 'dest_city_id' => '6301']);
        // $o9Receipt = factory(Receipt::class)->create(['status_code' => 'o9', 'dest_city_id' => '6301']);
        // $o0Receipt = factory(Receipt::class)->create(['status_code' => 'o0', 'dest_city_id' => '6301']);
        $distributionManifest = factory(Manifest::class, 'distribution')->create(['dest_city_id' => '6301']);
        $distributionManifest->addReceipt($auReceipt);
        $distributionManifest->addReceipt($mrReceipt);
        $distributionManifest->addReceipt($o1Receipt);
        // $distributionManifest->addReceipt($o2Receipt);
        // $distributionManifest->addReceipt($o3Receipt);
        // $distributionManifest->addReceipt($o4Receipt);
        // $distributionManifest->addReceipt($o5Receipt);
        // $distributionManifest->addReceipt($o6Receipt);
        // $distributionManifest->addReceipt($o7Receipt);
        // $distributionManifest->addReceipt($o8Receipt);
        // $distributionManifest->addReceipt($o9Receipt);
        // $distributionManifest->addReceipt($o0Receipt);

        $this->assertCount(3, $distributionManifest->fresh()->receipts);
        // $this->assertCount(12, $distributionManifest->fresh()->receipts);
    }

    /** @test */
    public function de_rw_rd_receipt_can_added_to_delivery_manifest()
    {
        $deReceipt = factory(Receipt::class)->create(['status_code' => 'de']);
        $rwReceipt = factory(Receipt::class)->create(['status_code' => 'rw']);
        $rdReceipt = factory(Receipt::class)->create(['status_code' => 'rd']);
        $deliveryManifest = factory(Manifest::class, 'delivery')->create();
        $deliveryManifest->addReceipt($deReceipt);
        $deliveryManifest->addReceipt($rwReceipt);
        $deliveryManifest->addReceipt($rdReceipt);

        $this->assertCount(3, $deliveryManifest->fresh()->receipts);
    }

    /** @test */
    public function dl_bd_receipt_can_added_to_return_manifest()
    {
        $deReceipt = factory(Receipt::class)->create(['status_code' => 'de', 'network_id' => 111]);
        $dlReceipt = factory(Receipt::class)->create(['status_code' => 'dl', 'network_id' => 111]);
        $bdReceipt = factory(Receipt::class)->create(['status_code' => 'bd', 'network_id' => 111]);
        $returnManifest = factory(Manifest::class, 'return')->create(['dest_network_id' => 111]);
        $returnManifest->addReceipt($deReceipt);
        $returnManifest->addReceipt($dlReceipt);
        $returnManifest->addReceipt($bdReceipt);

        $this->assertCount(2, $returnManifest->fresh()->receipts);
    }

    /** @test */
    public function dl_bd_rt_receipt_can_added_to_accounting_manifest()
    {
        $dlReceipt = factory(Receipt::class)->create(['status_code' => 'dl']);
        $bdReceipt = factory(Receipt::class)->create(['status_code' => 'bd']);
        $rtReceipt = factory(Receipt::class)->create(['status_code' => 'rt']);
        $o6Receipt = factory(Receipt::class)->create(['status_code' => 'o6']);
        $pdReceipt = factory(Receipt::class)->create(['status_code' => 'pd']);
        $accountingManifest = factory(Manifest::class, 'accounting')->create();
        $accountingManifest->addReceipt($dlReceipt);
        $accountingManifest->addReceipt($bdReceipt);
        $accountingManifest->addReceipt($rtReceipt);
        $accountingManifest->addReceipt($o6Receipt);
        $accountingManifest->addReceipt($pdReceipt);

        $this->assertCount(4, $accountingManifest->fresh()->receipts);
    }
}
