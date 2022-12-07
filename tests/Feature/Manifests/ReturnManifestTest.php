<?php

namespace Tests\Feature\Manifests;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use App\Events\Manifests\ReturnSent;
use App\Events\Manifests\ReturnReceived;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReturnManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_service_can_make_a_return_manifest_to_other_network()
    {
        $customerService = $this->loginAsCustomerService();
        $destNetwork = factory(Network::class)->states('province')->create(['name' => 'SHP Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);

        $this->visit(route('manifests.returns.create'));
        $this->select($destNetwork->id, 'dest_network_id');
        $this->type('', 'notes');
        $this->press(trans('manifest.returns.create'));
        $this->see(trans('manifest.receipt_lists'));

        $manifestNumber = 'M4'.$customerService->network->code.date('ym').'00001';
        $this->seePageIs(route('manifests.returns.show', $manifestNumber));
        $this->see(trans('manifest.created'));

        $this->seeInDatabase('manifests', [
            'number'           => $manifestNumber,
            'type_id'          => 4,
            'weight'           => null,
            'pcs_count'        => null,
            'orig_network_id'  => $customerService->network_id,
            'dest_network_id'  => $destNetwork->id,
            'creator_id'       => $customerService->id,
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
    public function customer_service_can_add_receipt_to_return_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $receiptOriginNetwork = factory(Network::class)->create();
        $receipt = factory(Receipt::class)->create([
            'status_code' => 'bd',
            'network_id'  => $receiptOriginNetwork->id,
        ]);

        $manifest = factory(Manifest::class, 'return')->create([
            'orig_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
            'dest_network_id' => $receiptOriginNetwork->id,
        ]);

        $this->visit(route('manifests.returns.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.returns.show', $manifest->number));
        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'or',
            'creator_id'          => $customerService->id,
            'creator_location_id' => $customerService->network->origin_city_id,
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function customer_service_can_only_add_correct_network_that_owned_receipt_to_return_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $receiptOriginNetwork = factory(Network::class)->create();
        $anotherNetwork = factory(Network::class)->create();
        $receipt = factory(Receipt::class)->create([
            'status_code' => 'bd',
            'network_id'  => $receiptOriginNetwork->id,
        ]);

        $manifest = factory(Manifest::class, 'return')->create([
            'orig_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
            'dest_network_id' => $anotherNetwork->id,
        ]);

        $this->visit(route('manifests.returns.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.returns.show', $manifest->number));
        $this->see(trans('manifest.receipt_addition_fails'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function customer_service_can_delete_an_unsent_return_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $manifest = factory(Manifest::class, 'return')->create([
            'orig_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
        ]);
        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.returns.show', $manifest->number));

        $this->click(trans('manifest.edit'));
        $this->seePageIs(route('manifests.returns.edit', $manifest->number));
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
    public function a_return_manifest_can_be_sent_out_if_has_any_receipts()
    {
        $customerService = $this->loginAsCustomerService();
        $receiptOriginNetwork = factory(Network::class)->create();
        $manifest = factory(Manifest::class, 'return')->create([
            'creator_id'      => $customerService->id,
            'dest_network_id' => $receiptOriginNetwork->id,
            'orig_network_id' => $customerService->network_id,
        ]);
        $receipt = factory(Receipt::class)->create([
            'status_code' => 'dl',
            'network_id'  => $receiptOriginNetwork->id,
        ]);
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.returns.show', $manifest->number));

        $this->expectsEvents(ReturnSent::class);
        $this->press(trans('manifest.send'));
        $this->seePageIs(route('manifests.returns.show', $manifest->number));
        $this->see(trans('manifest.sent'));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'or',
            'creator_id'          => $customerService->id,
            'creator_location_id' => $customerService->network->origin_city_id,
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function sent_return_manifest_can_be_receive_by_customer_service_user()
    {
        $origCustomerService = $this->loginAsCustomerService();
        $destNetwork = factory(Network::class)->states('province')->create(['name' => 'SHP Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $customerService = factory(User::class)->states('customer_service')->create(['network_id' => $destNetwork->id]);

        $manifest = factory(Manifest::class, 'return')->create([
            'creator_id'      => $origCustomerService->id,
            'orig_network_id' => $origCustomerService->network_id,
            'dest_network_id' => $destNetwork->id,
        ]);

        $receipt = factory(Receipt::class)->create([
            'status_code' => 'bd',
            'network_id'  => $destNetwork->id,
        ]);
        $manifest->addReceipt($receipt);
        $this->expectsEvents(ReturnSent::class);
        $manifest->send();

        $this->assertFalse($manifest->isReceived());

        $this->actingAs($customerService);

        $this->visit(route('manifests.receive', $manifest->number));
        $this->submitForm(trans('manifest.check_receipt'), [
            'receipt_number' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_pass'));

        $this->expectsEvents(ReturnReceived::class);
        $this->press(trans('manifest.receive'));
        $manifest = $manifest->fresh();
        $this->assertTrue($manifest->isReceived());
        $this->see(trans('manifest.received'));
        $this->seePageIs(route('manifests.returns.show', $manifest->number));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $origCustomerService->id,
            'creator_location_id' => $origCustomerService->network->origin_city_id,
            'start_status'        => 'or',
            'handler_id'          => $customerService->id,
            'handler_location_id' => $customerService->network->origin->id,
            'end_status'          => 'rt',
        ]);
    }

    /** @test */
    public function sales_counter_can_edit_an_unsent_return_manifest()
    {
        $origCustomerService = $this->loginAsCustomerService();
        $destNetwork = factory(Network::class)->states('province')->create(['name' => 'SHP Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $customerService = factory(User::class)->states('customer_service')->create(['network_id' => $destNetwork->id]);

        $manifest = factory(Manifest::class, 'return')->create([
            'creator_id'      => $origCustomerService->id,
            'orig_network_id' => $origCustomerService->network_id,
            'dest_network_id' => $destNetwork->id,
        ]);

        $this->visit(route('manifests.returns.show', $manifest->number));

        $this->click(trans('manifest.edit'));
        $this->submitForm(trans('manifest.update'), [
            'dest_network_id' => $manifest->dest_network_id,
            'notes'           => 'Donec sollicitudin molestie malesuada. Curabitur aliquet quam id dui posuere blandit.',
        ]);

        $this->see(trans('manifest.updated'));
        $this->seeInDatabase('manifests', [
            'id'              => $manifest->id,
            'dest_network_id' => $manifest->dest_network_id,
            'notes'           => 'Donec sollicitudin molestie malesuada. Curabitur aliquet quam id dui posuere blandit.',
        ]);
    }

    /** @test */
    public function sales_counter_cannot_edit_a_sent_return_manifest()
    {
        $origCustomerService = $this->loginAsCustomerService();
        $destNetwork = factory(Network::class)->states('province')->create(['name' => 'SHP Kalteng', 'code' => '62000000', 'origin_city_id' => '6271']);
        $customerService = factory(User::class)->states('customer_service')->create(['network_id' => $destNetwork->id]);

        $manifest = factory(Manifest::class, 'return')->create([
            'creator_id'      => $origCustomerService->id,
            'orig_network_id' => $origCustomerService->network_id,
            'dest_network_id' => $destNetwork->id,
        ]);

        $receipt = factory(Receipt::class)->create([
            'status_code' => 'bd',
            'network_id'  => $destNetwork->id,
        ]);
        $manifest->addReceipt($receipt);
        $manifest->send();

        // Visit manifest detail and NO Edit Button
        $this->visit(route('manifests.returns.show', $manifest->number));
        $this->dontSeeElement('a', ['href' => route('manifests.returns.edit', $manifest->number)]);

        // Visit manifest edit page and redirected to manifest detail and see warning
        $this->visit(route('manifests.returns.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.returns.show', $manifest->number));
    }
}
