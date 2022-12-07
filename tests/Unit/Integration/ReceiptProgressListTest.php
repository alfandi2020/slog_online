<?php

namespace Tests\Unit\Integration;

use App\Entities\Manifests\Manifest;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptProgressListTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function receipt_has_one_progress_after_creation()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertCount(1, $receipt->progressList());
    }

    /** @test */
    public function receipt_can_be_found_by_number()
    {
        $receipt = factory(Receipt::class)->create();
        $this->assertEquals(Receipt::find($receipt->id), Receipt::findByNumber($receipt->number));
    }

    /** @test */
    public function receipt_has_progresses_after_warehouse_manifest_sent()
    {
        $receipt = factory(Receipt::class)->create(['creator_id' => 3, 'orig_city_id' => '6371']);
        $manifest = factory(Manifest::class, 'handover')->create(['creator_id' => 3, 'orig_network_id' => 1, 'dest_network_id' => 1]);
        $manifest->addReceipt($receipt);

        $manifest->send();

        $receipt = $receipt->fresh();
        $this->assertCount(2, $receipt->progressList());
    }

    /** @test */
    public function receipt_has_progresses_after_warehouse_manifest_received()
    {
        $receipt = factory(Receipt::class)->create(['creator_id' => 3, 'orig_city_id' => '6371']);
        $manifest = factory(Manifest::class, 'handover')->create(['creator_id' => 3, 'orig_network_id' => 1, 'dest_network_id' => 1]);
        $manifest->addReceipt($receipt);

        $manifest->send();

        auth()->loginUsingId(4);
        $manifest->checkReceipt($receipt->number);
        $manifest->receive();

        $receipt = $receipt->fresh();
        $this->assertCount(3, $receipt->progressList());
    }

    /** @test */
    public function receipt_has_progresses_after_delivery_manifest_sent()
    {
        $receipt = factory(Receipt::class)->create(['creator_id' => 3, 'orig_city_id' => '6371']);
        $handoverManifest = factory(Manifest::class, 'handover')->create(['creator_id' => 3, 'orig_network_id' => 1, 'dest_network_id' => 1]);
        $handoverManifest->addReceipt($receipt);

        $handoverManifest->send();

        auth()->loginUsingId(4);
        $handoverManifest->checkReceipt($receipt->number);
        $handoverManifest->receive();

        $network = factory(Network::class)->states('province')->create(['origin_city_id' => '6271']);
        $deliveryManifest = factory(Manifest::class, 'delivery')->create(['creator_id' => 4, 'orig_network_id' => 1, 'dest_network_id' => $network->id]);
        $deliveryManifest->addReceipt($receipt);

        $deliveryManifest->send();

        $receipt = $receipt->fresh();
        $this->assertCount(4, $receipt->progressList());
    }

    /** @test */
    public function receipt_has_progresses_after_delivery_manifest_has_received()
    {
        $receipt = factory(Receipt::class)->create(['creator_id' => 3, 'orig_city_id' => '6371']);
        $handoverManifest = factory(Manifest::class, 'handover')->create(['creator_id' => 3, 'orig_network_id' => 1, 'dest_network_id' => 1]);
        $handoverManifest->addReceipt($receipt);

        $handoverManifest->send();

        auth()->loginUsingId(4);
        $handoverManifest->checkReceipt($receipt->number);
        $handoverManifest->receive();

        $network = factory(Network::class)->states('province')->create(['origin_city_id' => '6271']);
        $deliveryManifest = factory(Manifest::class, 'delivery')->create(['creator_id' => 4, 'orig_network_id' => 1, 'dest_network_id' => $network->id]);
        $deliveryManifest->addReceipt($receipt);

        $deliveryManifest->send();

        $destWarehouse = factory(User::class)->states('warehouse')->create(['network_id' => $network->id]);
        $this->actingAs($destWarehouse);

        $deliveryManifest->checkReceipt($receipt->number);
        $deliveryManifest->receive();

        $receipt = $receipt->fresh();
        $this->assertCount(5, $receipt->progressList());
        $this->assertTrue($receipt->progressIsAllClosed());
    }

    /** @test */
    public function a_receipt_model_can_have_on_manifest_progress_method()
    {
        $receipt = factory(Receipt::class)->create(['creator_id' => 3, 'orig_city_id' => '6371']);
        $manifest = factory(Manifest::class, 'handover')->create(['creator_id' => 3, 'orig_network_id' => 1, 'dest_network_id' => 1]);
        $manifest->addReceipt($receipt);

        $receipt = $receipt->fresh();
        $this->assertTrue($receipt->isOnManifestProgress());
        $this->assertFalse($receipt->progressIsAllClosed());
    }
}
