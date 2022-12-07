<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Entities\Receipts\Item;
use App\Entities\Receipts\Receipt;
use App\Services\ReceiptCollection;
use App\Services\Facades\ReceiptCollection as RCFacade;

class ReceiptCollectionTest extends TestCase
{
    /** @test */
    public function it_has_a_default_instance()
    {
        $receiptCollection = new ReceiptCollection;
        $this->assertEquals('new_receipts', $receiptCollection->currentInstance());
    }

    /** @test */
    public function it_can_have_multiple_instances()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt1 = new Receipt;
        $receipt2 = new Receipt;

        $receiptCollection->addReceipt($receipt1);
        $receiptCollection->addReceipt($receipt2);

        $receiptCollection->instance('wishlist')->addReceipt($receipt1);
        $receiptCollection->instance('wishlist')->addReceipt($receipt2);

        $this->assertCount(2, $receiptCollection->instance('new_receipts')->content());
        $this->assertCount(2, $receiptCollection->instance('wishlist')->content());
    }

    /** @test */
    public function collected_receipt_has_type()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt = new Receipt;

        $receiptCollection->addReceipt($receipt);

        $lastReceipt = $receiptCollection->content()->last();

        $this->assertEquals('Retail', $lastReceipt->type);
    }

    /** @test */
    public function it_has_facade()
    {
        $receiptCollection = new ReceiptCollection;
        $this->assertEquals(RCFacade::currentInstance(), $receiptCollection->currentInstance());
    }

    /** @test */
    public function it_consist_of_many_receipts()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt = new Receipt;

        $receiptCollection->addReceipt($receipt);
        $receiptCollection->addReceipt($receipt);
        $receiptCollection->addReceipt($receipt);

        $this->assertCount(3, $receiptCollection->content());
        $this->assertTrue($receiptCollection->hasContent());
    }

    /** @test */
    public function it_can_get_a_receipt_by_key()
    {
        $receipt = new Receipt;
        $receiptCollection = new ReceiptCollection;

        $receiptCollection->addReceipt($receipt);
        $gottenReceipt = $receiptCollection->get($receipt->receiptKey);
        $invalidReceipt = $receiptCollection->get('random_key');

        $this->assertEquals($receipt, $gottenReceipt);
        $this->assertNull($invalidReceipt);
    }

    /** @test */
    public function it_can_remove_receipt_from_receipt_collection()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt1 = new Receipt;
        $receipt2 = new Receipt;

        $receiptCollection->addReceipt($receipt1);
        $receiptCollection->addReceipt($receipt2);

        $this->assertCount(2, $receiptCollection->content());
        $receiptCollection->removeReceipt($receiptCollection->content()->keys()->last());
        $this->assertCount(1, $receiptCollection->content());
    }

    /** @test */
    public function it_can_be_empty_out()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt1 = new Receipt;
        $receipt2 = new Receipt;

        $receiptCollection->addReceipt($receipt1);
        $receiptCollection->addReceipt($receipt1);
        $receiptCollection->addReceipt($receipt1);
        $receiptCollection->addReceipt($receipt2);
        $receiptCollection->addReceipt($receipt2);

        $this->assertCount(5, $receiptCollection->content());
        $receiptCollection->destroy();

        $this->assertCount(0, $receiptCollection->content());
        $this->assertTrue($receiptCollection->isEmpty());
    }

    /** @test */
    public function it_has_content_keys()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt1 = new Receipt;
        $receipt2 = new Receipt;

        $receiptCollection->addReceipt($receipt1);
        $receiptCollection->addReceipt($receipt2);

        $this->assertCount(2, $receiptCollection->keys());
        $receiptCollection->removeReceipt($receiptCollection->content()->keys()->last());
        $this->assertCount(1, $receiptCollection->keys());
    }

    /** @test */
    public function it_can_update_a_receipt_attributes()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt = $receiptCollection->addReceipt(new Receipt);
        $this->assertCount(1, $receiptCollection->content());

        $newReceiptData = [
            'pc_count'       => 2,
            'charged_weight' => 1,
            'charged_on'     => 2,
            'be_insured'     => 0,
            'discount'       => 0,
            'packing_cost'   => 0,
        ];

        $receiptCollection->updateReceiptData($receipt->receiptKey, $newReceiptData);
        $this->assertArrayHasKey('pc_count', $receipt->toArray());
        $this->assertArrayHasKey('charged_weight', $receipt->toArray());
        $this->assertArrayHasKey('charged_on', $receipt->toArray());
        $this->assertArrayHasKey('be_insured', $receipt->toArray());
        $this->assertArrayHasKey('discount', $receipt->toArray());
        $this->assertArrayHasKey('packing_cost', $receipt->toArray());
    }

    /** @test */
    public function it_can_add_item_to_receipt()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt = $receiptCollection->addReceipt(new Receipt);
        $this->assertCount(1, $receiptCollection->content());

        $receiptCollection->addItemToReceipt($receipt->receiptKey, new Item(2));
        $this->assertEquals(2, $receipt->getChargedWeight());
    }

    /** @test */
    public function it_can_update_an_item_of_receipt()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt = $receiptCollection->addReceipt(new Receipt);
        $this->assertCount(1, $receiptCollection->content());

        $receiptCollection->addItemToReceipt($receipt->receiptKey, new Item(1));
        $this->assertCount(1, $receipt->items());
        $this->assertEquals(1, $receipt->getChargedWeight());

        $newItemData = [
            'weight'  => 1,
            'length'  => 20,
            'width'   => 20,
            'height'  => 20,
            'type_id' => 2,
            'notes'   => '',
        ];

        $receiptCollection->updateReceiptItem($receipt->receiptKey, 0, $newItemData);
        $this->assertEquals(2, $receipt->getChargedWeight());
    }

    /** @test */
    public function it_can_remove_item_from_receipt()
    {
        $receiptCollection = new ReceiptCollection;

        $receipt = $receiptCollection->addReceipt(new Receipt);
        $this->assertCount(1, $receiptCollection->content());

        $receiptCollection->addItemToReceipt($receipt->receiptKey, new Item(2));
        $this->assertCount(1, $receipt->items());
        $receiptCollection->removeItemFromReceipt($receipt->receiptKey, 0);
        $this->assertCount(0, $receipt->items());
        $this->assertEquals(null, $receipt->getChargedWeight());
    }
}
