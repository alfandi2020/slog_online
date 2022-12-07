<?php

namespace Tests\Unit\Integration;

use Tests\TestCase;
use App\Entities\Users\User;
use App\Entities\Receipts\Receipt;
use App\Entities\Customers\Customer;
use App\Entities\Manifests\Manifest;
use App\Entities\Networks\DeliveryUnit;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManifestTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_receipt_can_only_added_once_per_menifest()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);
        $manifest = Manifest::findOrFail($manifest->id);
        $this->assertCount(1, $manifest->receipts);
        $manifest->addReceipt($receipt);
        $this->assertCount(1, $manifest->receipts);
        $this->assertDatabaseHas('receipt_progress', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $manifest->id,
            'start_status' => 'mw',
            'handler_id'   => null,
            'end_status'   => null,
        ]);
    }

    /** @test */
    public function a_receipt_can_be_removed_from_a_manfiest()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);
        $manifest = Manifest::findOrFail($manifest->id);
        $this->assertCount(1, $manifest->receipts);
        $manifest->removeReceipt($receipt);
        $manifest = Manifest::findOrFail($manifest->id);
        $this->assertCount(0, $manifest->receipts);
    }

    /** @test */
    public function manifest_can_be_sent_if_it_has_receipts()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $receipt = factory(Receipt::class)->create();

        $manifest->addReceipt($receipt);
        $manifest->send();

        $this->assertTrue($manifest->isSent());
    }

    /** @test */
    public function manifest_cannot_be_sent_if_it_has_no_receipts()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $manifest->send();

        $this->assertFalse($manifest->isSent());
    }

    /** @test */
    public function manifest_can_be_taken_back_if_it_has_been_sent()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $receipt = factory(Receipt::class)->create();

        $manifest->addReceipt($receipt);
        $manifest->send();

        $this->assertTrue($manifest->isSent());
        $manifest->takeBack();

        $this->assertFalse($manifest->isSent());
        $this->assertFalse($manifest->isReceived());
    }

    /** @test */
    public function manifest_can_be_received_if_it_has_no_receipts()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $receipt = factory(Receipt::class)->create();

        $manifest->addReceipt($receipt);
        $manifest->send();
        $manifest->receive();

        $this->assertTrue($manifest->isSent());
        $this->assertTrue($manifest->isReceived());
    }

    /** @test */
    public function manifest_can_be_found_by_number()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $this->assertEquals(Manifest::find($manifest->id), Manifest::findByNumber($manifest->number));
    }

    /** @test */
    public function manifest_can_check_type()
    {
        $manifest = factory(Manifest::class, 'handover')->create();
        $this->assertTrue($manifest->isTypeOf('handover'));
        $this->assertTrue($manifest->isTypeOf(['handover', 'return']));
    }

    /** @test */
    public function manifest_has_customer_name_presenter()
    {
        $customer = factory(Customer::class)->create();
        $manifest = factory(Manifest::class, 'accounting')->create(['customer_id' => $customer->id]);
        $this->assertTrue($manifest->isTypeOf('accounting'));
        $this->assertEquals($customer->name, $manifest->present()->customerName());
    }

    /** @test */
    public function manifest_has_customer_link_presenter()
    {
        $customer = factory(Customer::class)->create();
        $manifest = factory(Manifest::class, 'accounting')->create(['customer_id' => $customer->id]);
        $this->assertEquals(link_to_route('customers.show', $customer->name, [$customer->id]), $manifest->present()->customerLink());
    }

    /** @test */
    public function a_distribution_manifest_has_belongs_to_delivery_unit_relation()
    {
        $deliveryUnit = factory(DeliveryUnit::class)->create();
        $manifest = factory(Manifest::class, 'distribution')->create(['delivery_unit_id' => $deliveryUnit->id]);

        $this->assertInstanceOf(DeliveryUnit::class, $manifest->deliveryUnit);
        $this->assertEquals($manifest->delivery_unit_id, $manifest->deliveryUnit->id);
    }

    /** @test */
    public function a_delivery_manifest_has_belongs_to_delivery_courier_relation()
    {
        $deliveryCourier = factory(User::class)->states('courier')->create();
        $manifest = factory(Manifest::class, 'delivery')->create(['delivery_unit_id' => $deliveryCourier->id]);

        $this->assertInstanceOf(User::class, $manifest->deliveryCourier);
        $this->assertEquals($manifest->delivery_unit_id, $manifest->deliveryCourier->id);
    }
}
