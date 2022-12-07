<?php

namespace Tests\Feature\Manifests;

use Tests\BrowserKitTestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use App\Events\Manifests\HandoverSent;
use App\Events\Manifests\HandoverReceived;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HandoverManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sales_counter_can_make_a_handover_manifest_to_warehouse()
    {
        $salesCounter = $this->loginAsSalesCounter();

        $this->visit(route('manifests.handovers.create'));
        $this->type(10, 'weight');
        $this->type(10, 'pcs_count');
        $this->type('', 'notes');
        $this->press(trans('manifest.handovers.create'));
        $this->see(trans('manifest.receipt_lists'));

        $manifestNumber = 'M1'.$salesCounter->network->code.date('ym').'00001';
        $this->seePageIs(route('manifests.handovers.show', $manifestNumber));

        $this->seeInDatabase('manifests', [
            'number'           => $manifestNumber,
            'type_id'          => 1,
            'weight'           => 10,
            'pcs_count'        => 10,
            'orig_network_id'  => $salesCounter->network_id,
            'dest_network_id'  => $salesCounter->network_id,
            'creator_id'       => $salesCounter->id,
            'handler_id'       => null,
            'customer_id'      => null,
            'delivery_unit_id' => null,
            'start_km'         => null,
            'end_km'           => null,
            'deliver_at'       => null,
            'received_at'      => null,
            'notes'            => null,
        ]);
    }

    /** @test */
    public function sales_counter_can_add_receipt_to_handover_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create([
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $this->visit(route('manifests.handovers.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.handovers.show', $manifest->number));
        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'mw',
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.handovers.show', $manifest->number));
        $this->see(trans('manifest.receipt_addition_fails'));
    }

    /** @test */
    public function sales_counter_can_remove_receipt_from_handover_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create([
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.handovers.show', $manifest->number));
        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->see(trans('manifest.receipt_removed'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function sales_counter_can_delete_an_unsent_handover_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create([
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);
        $manifest->addReceipt($receipt);

        $this->seeInDatabase('manifests', ['number' => $manifest->number]);
        $this->visit(route('manifests.handovers.edit', $manifest->number));
        $this->press(trans('manifest.delete'));
        $this->see(trans('manifest.deleted'));
        $this->seePageIs(route('manifests.'.$manifest->pluralTypeCode().'.index'));

        $this->dontSeeInDatabase('manifests', ['number' => $manifest->number]);
        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function a_sent_handover_manifest_cannot_be_delete()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create();
        $manifest = factory(Manifest::class, 'handover')->create([
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);
        $manifest->addReceipt($receipt);
        $this->expectsEvents(HandoverSent::class);
        $manifest->send();

        $this->visit(route('manifests.handovers.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.handovers.show', $manifest->number));
    }

    /** @test */
    public function a_handover_manifest_can_be_sent_out_if_has_any_receipts()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipts = factory(Receipt::class, 2)->create();
        $manifest = factory(Manifest::class, 'handover')->create([
            'creator_id'      => $salesCounter->id,
            'orig_network_id' => $salesCounter->network_id,
        ]);
        $manifest->addReceipt($receipts[0]);
        $manifest->addReceipt($receipts[1]);

        $this->visit(route('manifests.handovers.show', $manifest->number));
        $this->see($receipts[0]->number);
        $this->see($receipts[1]->number);
        $this->see('<span class="label label-default">On Proccess</span>');
        $this->dontSee('<span class="label label-info">On Delivery</span>');

        $this->expectsEvents(HandoverSent::class);

        $this->press(trans('manifest.send'));
        $this->seePageIs(route('manifests.handovers.show', $manifest->number));
        $this->see(trans('manifest.sent'));

        $manifest = $manifest->fresh();

        $this->assertTrue($manifest->isSent());
        $this->assertFalse($manifest->isReceived());
        $this->see('<span class="label label-info">On Delivery</span>');
        $this->dontSee('<span class="label label-default">On Proccess</span>');
        $this->dontSee(trans('manifest.add_receipt'));
        $this->dontSee(trans('manifest.remove_receipt'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipts[0]->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'mw',
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipts[1]->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'mw',
            'creator_id'          => $salesCounter->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function a_sent_handover_manifest_can_be_receive_by_warehouse_user()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['creator_id' => $salesCounter->id, 'network_id' => $salesCounter->network_id]);

        $manifest = factory(Manifest::class, 'handover')->create([
            'creator_id'      => $salesCounter->id,
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
        ]);

        $manifest->addReceipt($receipt);
        $this->expectsEvents(HandoverSent::class);
        $manifest->send();

        $this->assertTrue($manifest->isSent());
        $this->assertFalse($manifest->isReceived());

        $warehouse = $this->loginAsWarehouse();

        $this->visit(route('manifests.receive', $manifest->number));
        $this->dontSeeElement('button', ['id' => 'receive_manifest']);
        $this->submitForm(trans('manifest.check_receipt'), [
            'receipt_number' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_pass'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $manifest->id,
            'creator_id'   => $salesCounter->id,
            'start_status' => 'mw',
            'handler_id'   => $warehouse->id,
            'end_status'   => 'rw',
        ]);

        $this->expectsEvents(HandoverReceived::class);
        $this->seeElement('button', ['id' => 'receive_manifest']);

        $this->press(trans('manifest.receive'));
        $this->see('<span class="label label-success">Received</span>');
        $this->see(trans('manifest.received'));
    }

    /** @test */
    public function user_can_edit_handover_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();

        $manifest = factory(Manifest::class, 'handover')->create([
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $this->visit(route('manifests.handovers.show', $manifest->number));
        $this->click(trans('manifest.edit'));

        $this->submitForm(trans('manifest.update'), [
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        ]);

        $this->seeInDatabase('manifests', [
            'id'    => $manifest->id,
            'notes' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
        ]);
    }

    /** @test */
    public function sales_counter_cannot_edit_a_sent_handover_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'handover')->create([
            'orig_network_id' => $salesCounter->network_id,
            'dest_network_id' => $salesCounter->network_id,
            'creator_id'      => $salesCounter->id,
        ]);

        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);
        $manifest->send();

        // Visit manifest detail and NO Edit Button
        $this->visit(route('manifests.handovers.show', $manifest->number));
        $this->dontSeeElement('a', ['href' => route('manifests.handovers.edit', $manifest->number)]);

        // Visit manifest edit page and redirected to manifest detail and see warning
        $this->visit(route('manifests.handovers.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.handovers.show', $manifest->number));
    }
}
