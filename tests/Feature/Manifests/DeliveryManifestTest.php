<?php

namespace Tests\Feature\Manifests;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use App\Events\Manifests\DeliverySent;
use App\Events\Manifests\DeliveryReceived;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeliveryManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function warehouse_can_make_a_delivery_manifest_to_other_network()
    {
        $warehouseUser = $this->loginAsWarehouse();
        $destNetwork = factory(Network::class)
            ->states('province')
            ->create([
                'name'           => 'BAM Kalteng',
                'code'           => '62000000',
                'origin_city_id' => '6271',
            ]);
        $courierUser = factory(User::class)->states('courier')->create();

        $this->visit(route('manifests.deliveries.create'));
        $this->submitForm(__('manifest.deliveries.create'), [
            'dest_network_id'  => $destNetwork->id,
            'delivery_unit_id' => $courierUser->id,
            'weight'           => 10,
            'pcs_count'        => 10,
            'notes'            => '',
        ]);

        $this->see(__('manifest.receipt_lists'));
        $this->seeText($courierUser->name);

        $manifestNumber = 'M2'.$warehouseUser->network->code.date('ym').'00001';
        $this->seePageIs(route('manifests.deliveries.show', $manifestNumber));

        $this->seeInDatabase('manifests', [
            'number'           => $manifestNumber,
            'type_id'          => 2,
            'weight'           => 10,
            'pcs_count'        => 10,
            'orig_network_id'  => $warehouseUser->network_id,
            'dest_network_id'  => $destNetwork->id,
            'creator_id'       => $warehouseUser->id,
            'handler_id'       => null,
            'customer_id'      => null,
            'delivery_unit_id' => $courierUser->id,
            'start_km'         => null,
            'end_km'           => null,
            'deliver_at'       => null,
            'received_at'      => null,
            'notes'            => null,
        ]);
    }

    /** @test */
    public function sales_counter_can_add_receipt_to_delivery_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create();

        $manifest = factory(Manifest::class, 'delivery')->create([
            'orig_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $this->visit(route('manifests.deliveries.show', $manifest->number));
        $this->submitForm(__('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.deliveries.show', $manifest->number));
        $this->see(__('manifest.receipt_added'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'start_status'        => 'mn',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function sales_counter_can_delete_an_unsent_delivery_manifest_and_detach_receipts()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'delivery')->create([
            'orig_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.deliveries.show', $manifest->number));

        $this->click(__('manifest.edit'));
        $this->seePageIs(route('manifests.deliveries.edit', $manifest->number));
        $this->press(__('manifest.delete'));
        $this->see(__('manifest.deleted'));

        // Manifest Deleted
        $this->dontSeeInDatabase('manifests', ['id' => $manifest->id]);

        // Receipt detached from manifest
        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function a_delivery_manifest_can_be_sent_out_if_has_any_receipts()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'delivery')->create([
            'creator_id'      => $salesCounter->id,
            'orig_network_id' => $salesCounter->network_id,
        ]);
        $receipts = factory(Receipt::class, 2)->create();
        $manifest->addReceipt($receipts[0]);
        $manifest->addReceipt($receipts[1]);

        $this->visit(route('manifests.deliveries.show', $manifest->number));

        $this->expectsEvents(DeliverySent::class);
        $this->press(__('manifest.send'));
        $this->seePageIs(route('manifests.deliveries.show', $manifest->number));
        $this->see(__('manifest.sent'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipts[0]->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'start_status'        => 'mn',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipts[1]->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'start_status'        => 'mn',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function sent_delivery_manifest_can_be_receive_by_sales_counter_user()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'delivery')->create([
            'creator_id'      => $salesCounter->id,
            'dest_network_id' => $salesCounter->network_id,
        ]);

        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);
        $this->expectsEvents(DeliverySent::class);
        $manifest->send();

        $this->assertFalse($manifest->isReceived());

        $this->visit(route('manifests.receive', $manifest->number));
        $this->submitForm(__('manifest.check_receipt'), [
            'receipt_number' => $receipt->number,
        ]);
        $this->see(__('manifest.receipt_pass'));

        $this->expectsEvents(DeliveryReceived::class);
        $this->press(__('manifest.receive'));
        $this->see(__('manifest.received'));
        $this->seePageIs(route('manifests.deliveries.show', $manifest->number));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'start_status'        => 'mn',
            'handler_id'          => $salesCounter->id,
            'handler_location_id' => $salesCounter->network->origin->id,
            'end_status'          => 'rd',
        ]);
    }

    /** @test */
    public function sales_counter_can_edit_an_unsent_delivery_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'delivery')->create([
            'orig_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);
        $courierUser = factory(User::class)->states('courier')->create();

        $this->visit(route('manifests.deliveries.show', $manifest->number));
        $this->dontSeeText($courierUser->name);

        $this->click(__('manifest.edit'));
        $this->submitForm(__('manifest.update'), [
            'dest_network_id'  => $manifest->dest_network_id,
            'delivery_unit_id' => $courierUser->id,
            'weight'           => 123,
            'pcs_count'        => 10,
            'notes'            => 'Donec sollicitudin molestie malesuada. Curabitur aliquet quam id dui posuere blandit.',
        ]);
        $this->see(__('manifest.updated'));
        $this->seeText($courierUser->name);

        $this->seeInDatabase('manifests', [
            'id'               => $manifest->id,
            'dest_network_id'  => $manifest->dest_network_id,
            'delivery_unit_id' => $courierUser->id,
            'weight'           => 123,
            'pcs_count'        => 10,
            'notes'            => 'Donec sollicitudin molestie malesuada. Curabitur aliquet quam id dui posuere blandit.',
        ]);
    }

    /** @test */
    public function sales_counter_cannot_edit_a_sent_delivery_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'delivery')->create([
            'orig_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);
        $manifest->send();

        // Visit manifest detail and NO Edit Button
        $this->visit(route('manifests.deliveries.show', $manifest->number));
        $this->dontSeeElement('a', ['href' => route('manifests.deliveries.edit', $manifest->number)]);

        // Visit manifest edit page and redirected to manifest detail and see warning
        $this->visit(route('manifests.deliveries.edit', $manifest->number));
        $this->see(__('manifest.uneditable'));
        $this->seePageIs(route('manifests.deliveries.show', $manifest->number));
    }
}
