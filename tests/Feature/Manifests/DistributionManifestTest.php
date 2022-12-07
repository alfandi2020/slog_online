<?php

namespace Tests\Feature\Manifests;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use App\Entities\Networks\DeliveryUnit;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DistributionManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sales_counter_can_make_distribution_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $courier = User::find(7); // Seeded Courier User
        $deliveryUnit = factory(DeliveryUnit::class)->create(['network_id' => $salesCounter->network_id]);

        $this->visit(route('manifests.distributions.create'));
        $this->select('6372', 'dest_city_id');
        $this->select($courier->id, 'handler_id');
        $this->select($deliveryUnit->id, 'delivery_unit_id');
        $this->type('', 'notes');
        $this->press(trans('manifest.distributions.create'));

        $manifestNumber = 'M3'.$salesCounter->network->code.date('ym').'00001';
        $this->seePageIs(route('manifests.distributions.show', $manifestNumber));
        $this->see(trans('manifest.created'));

        $this->seeInDatabase('manifests', [
            'number'           => $manifestNumber,
            'type_id'          => 3,
            'weight'           => null,
            'pcs_count'        => null,
            'orig_network_id'  => $salesCounter->network_id,
            'dest_network_id'  => $salesCounter->network_id,
            'creator_id'       => $salesCounter->id,
            'handler_id'       => $courier->id,
            'customer_id'      => null,
            'delivery_unit_id' => $deliveryUnit->id,
            'start_km'         => null,
            'end_km'           => null,
            'deliver_at'       => null,
            'received_at'      => null,
            'dest_city_id'     => '6372',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function warehouse_user_can_set_distribution_manfiest_to_on_delivery()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $courier = User::find(7); // Seeded Courier User
        $deliveryUnit = factory(DeliveryUnit::class)->create(['network_id' => $salesCounter->network_id]);

        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'       => $salesCounter->id,
            'handler_id'       => $courier->id,
            'delivery_unit_id' => $deliveryUnit->id,
            'notes'            => null,
            'dest_city_id'     => '6301',
        ]);

        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.distributions.show', $manifest->number));

        $this->type('2016-07-01 08:00', 'deliver_at');
        $this->type('100000', 'start_km');
        $this->type('Catatan', 'notes');
        $this->press(trans('manifest.send'));

        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
        $this->see(trans('manifest.sent'));

        $this->seeInDatabase('manifests', [
            'id'               => $manifest->id,
            'number'           => $manifest->number,
            'type_id'          => 3,
            'orig_network_id'  => $salesCounter->network_id,
            'dest_network_id'  => $salesCounter->network_id,
            'creator_id'       => $salesCounter->id,
            'handler_id'       => $courier->id,
            'weight'           => null,
            'pcs_count'        => null,
            'deliver_at'       => '2016-07-01 08:00:00',
            'received_at'      => null,
            'delivery_unit_id' => $deliveryUnit->id,
            'start_km'         => 100000,
            'end_km'           => null,
            'notes'            => 'Catatan',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $courier->id,
            'creator_location_id' => $salesCounter->network->origin_city_id,
            'start_status'        => 'od',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);

        $this->assertTrue($receipt->fresh()->hasStatusOf(['od']));
    }

    /** @test */
    public function handle_null_dest_city_id_on_distribution_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'distribution')->create([
            'dest_city_id' => null, // Kota Banjarbaru
            'creator_id'   => $salesCounter->id,
        ]);

        $this->visit(route('manifests.distributions.show', $manifest->number));
        $this->see("<th>".trans('manifest.distributions.dest_city')."</th>\n<th class=\"text-primary\">-</th>");
    }

    /** @test */
    public function can_edit_if_it_unsent()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'distribution')->create([
            'dest_city_id' => '6372', // Kota Banjarbaru
            'creator_id'   => $salesCounter->id,
        ]);
        $courier = User::find(7); // Seeded Courier User
        $deliveryUnit = factory(DeliveryUnit::class)->create(['network_id' => $salesCounter->network_id]);

        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->select('6301', 'dest_city_id');
        $this->select($courier->id, 'handler_id');
        $this->select($deliveryUnit->id, 'delivery_unit_id');
        $this->type('2016-07-01 08:00', 'deliver_at');
        // $this->type('2016-07-02 08:00', 'received_at');
        $this->type('100000', 'start_km');
        // $this->type('100200', 'end_km');
        $this->type('', 'notes');
        $this->press(trans('manifest.distributions.update'));

        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
        $this->see(trans('manifest.updated'));

        $this->seeInDatabase('manifests', [
            'id'               => $manifest->id,
            'number'           => $manifest->number,
            'type_id'          => 3,
            'orig_network_id'  => $salesCounter->network_id,
            'dest_network_id'  => $salesCounter->network_id,
            'creator_id'       => $salesCounter->id,
            'handler_id'       => $courier->id,
            'weight'           => null,
            'pcs_count'        => null,
            'deliver_at'       => '2016-07-01 08:00:00',
            // 'received_at' => '2016-07-02 08:00:00',
            'received_at'      => null,
            'delivery_unit_id' => $deliveryUnit->id,
            'start_km'         => 100000,
            // 'end_km' => 100200,
            'end_km'           => null,
            'dest_city_id'     => '6301',
            'notes'            => null,
        ]);
    }

    /** @test */
    public function cannot_edit_if_it_has_sent()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest->addReceipt($receipt);
        $manifest->send();

        // Visit manifest detail and NO Edit Button
        $this->visit(route('manifests.distributions.show', $manifest->number));
        $this->dontSeeElement('a', ['href' => route('manifests.distributions.edit', $manifest->number)]);

        // Visit manifest edit page and redirected to manifest detail and see warning
        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
    }

    /** @test */
    public function can_edit_destination_city_if_it_has_receipts_but_dest_city_id_is_null()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => null,
        ]);

        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->see('<select class="form-control" required id="dest_city_id" name="dest_city_id">');
        $this->dontSee('<label for="Kota/Kab. Tujuan" class="control-label">'.trans('manifest.distributions.dest_city').'</label><div class="form-control" readonly>');
    }

    /** @test */
    public function can_edit_destination_city_if_it_that_has_no_receipts()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->see('<select class="form-control" required id="dest_city_id" name="dest_city_id">');
        $this->dontSee('<label for="Kota/Kab. Tujuan" class="control-label">'.trans('manifest.distributions.dest_city').'</label><div class="form-control" readonly>'.$manifest->destinationCity->name.'</div>');
    }

    /** @test */
    public function can_edit_destination_city_if_it_has_dest_city_and_receipts()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->dontSee('<select class="form-control" required id="dest_city_id" name="dest_city_id">');
        $this->see('<label for="Kota/Kab. Tujuan" class="control-label">'.trans('manifest.distributions.dest_city').'</label><div class="form-control" readonly>'.$manifest->destinationCity->name.'</div>');
    }

    /** @test */
    public function can_add_receipt_to_distribution_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $courier = User::find(7); // Seeded Courier User
        $manifest = factory(Manifest::class, 'distribution')->create([
            'handler_id'   => $courier->id,
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $this->visit(route('manifests.distributions.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'od',
            'creator_id'          => $courier->id,
            'creator_location_id' => '6371', // Kota Banjarmasin, Seeded sales counter origin city
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
        $this->see(trans('manifest.receipt_addition_fails'));
    }

    /** @test */
    public function cannot_add_receipt_if_it_has_different_dest_city_id()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6371']);
        $courier = User::find(7); // Seeded Courier User
        $manifest = factory(Manifest::class, 'distribution')->create([
            'handler_id'   => $courier->id,
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6372',
        ]);

        $this->visit(route('manifests.distributions.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
        $this->see(trans('manifest.receipt_addition_fails'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function can_remove_receipt_from_distribution_manifest()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.distributions.show', $manifest->number));
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
    public function can_delete_if_it_is_unsent()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->press(trans('manifest.delete'));
        $this->see(trans('manifest.deleted'));
        $this->seePageIs(route('manifests.distributions.index'));

        $this->dontSeeInDatabase('manifests', ['number' => $manifest->number]);
        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function cannot_be_deleted_if_it_has_sent()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $manifest = factory(Manifest::class, 'distribution')->create([
            'creator_id'   => $salesCounter->id,
            'dest_city_id' => '6301',
        ]);

        $manifest->addReceipt($receipt);
        $manifest->send();

        $this->visit(route('manifests.distributions.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.distributions.show', $manifest->number));
    }
}
