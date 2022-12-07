<?php

namespace Tests\Unit\Integration;

use App\Entities\Receipts\Item;
use App\Entities\Receipts\Receipt;
use App\Entities\References\Reference;
use App\Entities\Services\Rate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptItemsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function receipt_has_items()
    {
        $receipt1 = new Receipt;
        $receipt2 = new Receipt;

        $item1 = new Item;
        $item2 = new Item;

        $receipt1->addItem($item1);
        $receipt1->addItem($item2);

        $receipt2->addItem($item1);
        $receipt2->addItem($item2);

        // receipt_has_items
        $this->assertCount(2, $receipt1->items());

        // receipt_has_total_items_count
        $this->assertEquals(2, $receipt1->itemsCount());

        // receipt_has_items_in_colletion_format
        $this->assertEquals(collect([$item1, $item2]), $receipt1->items());

        // receipt_has_items_in_array_format
        $this->assertEquals([$item1->toArray(), $item2->toArray()], $receipt1->itemsArray());
    }

    /** @test */
    public function can_retrieve_first_and_last_receipt_items()
    {
        $receipt = new Receipt;

        $item1 = new Item;
        $item2 = new Item(2);

        $receipt->addItem($item1);
        $receipt->addItem($item2);

        $this->assertEquals($receipt->firstItem(), $item1);
        $this->assertNotEquals($receipt->firstItem(), $item2);
        $this->assertEquals($receipt->lastItem(), $item2);
        $this->assertNotEquals($receipt->lastItem(), $item1);
    }

    /** @test */
    public function it_can_remove_a_receipt_item()
    {
        $receipt = new Receipt;

        $item1 = new Item;
        $item2 = new Item;

        $receipt->addItem($item1);
        $receipt->addItem($item2);

        $randomIndex = rand(0, $receipt->itemsCount() - 1);
        $receipt->removeItem($randomIndex);
        $this->assertCount(1, $receipt->items());
    }

    /** @test */
    public function receipt_item_can_have_volume()
    {
        $item = new Item;
        $this->assertNull($item->getVolume());

        $item = new Item(1, 10, 20, 20);
        $this->assertEquals(4000, $item->getVolume());
    }

    /** @test */
    public function receipt_item_can_have_volumetric_weight()
    {
        // it has default volumetric devider of 6000
        $item = new Item(1, 20, 20, 20);
        $this->assertEquals((8000/6000), $item->getVolumetricWeight());

        // system can set volumetric devider
        $item = new Item(1, 20, 20, 20);
        $item->setVolumetricDevider(4000);
        $this->assertEquals(2, $item->getVolumetricWeight());
    }

    /** @test */
    public function it_sets_volumetric_devider_ti_4000_on_sal_service()
    {
        $item = new Item(1, 20, 20, 20);
        $this->assertEquals(2, $item->getVolumetricWeight('sal'));
    }

    /** @test */
    public function it_has_total_charged_weight()
    {
        $receipt = new Receipt;

        // charged on volumetric weight = 2
        $item1 = new Item(1, 30, 20, 20);
        // charged on volumetric weight = 2
        $item2 = new Item(1, 22, 15, 20);
        // charged on actual weight = 3
        $item3 = new Item(3, 30, 20, 20);

        $receipt->addItem($item1);
        $receipt->addItem($item2);
        $receipt->addItem($item3);

        $this->assertEquals(7, $receipt->getChargedWeight());
    }

    /** @test */
    public function receipt_item_can_have_charged_weight()
    {
        // charged on actual weight if no dimentions
        $item = new Item(2);
        $this->assertEquals(2, $item->getChargedWeight());

        // charged on volumetric weight if volumetric weight larger than actual weight
        $item = new Item(1, 20, 20, 20);
        $item->setVolumetricDevider(4000);
        $this->assertEquals(2, $item->getChargedWeight());

        // charged on actual weight if volumetric weight smaller than actual weight
        $item = new Item(3, 20, 20, 20);
        $item->setVolumetricDevider(4000);
        $this->assertEquals(3, $item->getChargedWeight());
    }

    /** @test */
    public function receipt_has_base_rate_and_base_charge()
    {
        $rate = factory(Rate::class, 'city_to_city')->create([
            'service_id' => 11, 'rate_kg' => 10000
        ]);
        $receipt = new Receipt;
        $receipt->setRate($rate);
        $receipt->charged_on = 1;

        // charged on volumetric weight = 2
        $item1 = new Item(1, 30, 20, 20);
        // charged on volumetric weight = 2
        $item2 = new Item(1, 22, 15, 20);
        // charged on actual weight = 3
        $item3 = new Item(3, 30, 20, 20);

        $this->assertEquals(0, $receipt->getCharge());

        $receipt->addItem($item1);
        $receipt->addItem($item2);
        $receipt->addItem($item3);

        $this->assertEquals(7, $receipt->getChargedWeight());
        $this->assertEquals(70000, $receipt->getCharge());
    }

    /** @test */
    public function receipt_item_can_have_type()
    {
        $type = Reference::find(1);
        $item = new Item(2, 20, 20, 20, $type->id);
        $this->assertEquals('Paket', $item->getType());
    }

    /** @test */
    public function it_can_update_a_receipt_item()
    {
        $receipt = new Receipt;
        $item = new Item(1, 20, 20, 20);

        $receipt->addItem($item);

        $newItemData = [
            'weight' => 2,
            'length' => '',
            'width' => '',
            'height' => '',
            'type_id' => 2,
            'notes' => '',
        ];

        $this->assertCount(1, $receipt->items());

        $receipt->updateItem(0, $newItemData);
        $updatedItem = $receipt->firstItem();
        $this->assertEquals(2, $updatedItem->weight);
        $this->assertEquals(null, $updatedItem->getVolume());
    }
}
