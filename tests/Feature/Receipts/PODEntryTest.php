<?php

namespace Tests\Feature\Receipts;

use Carbon\Carbon;
use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PODEntryTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_service_can_entry_receipt_pod_if_manifest_was_sent()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = User::find(7); // Seeded Courier user

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'handler_id'      => $courier->id,
            'dest_city_id'    => '6301',
        ]);
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $distributionManifest->addReceipt($receipt);
        $distributionManifest->send();

        $this->assertTrue($distributionManifest->isSent());

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $distributionManifest->id,
            'creator_id'   => $courier->id,
            'start_status' => 'od',
            'handler_id'   => null,
            'end_status'   => null,
        ]);

        $this->visit(route('pods.by-manifest'));
        $this->submitForm(__('manifest.search'), [
            'manifest_number' => $distributionManifest->number,
        ]);

        $this->seePageIs(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));
        $this->seeElement('a', ['id' => 'pod-entry-'.$receipt->id]);

        $this->assertFalse($distributionManifest->isAllDelivered());
    }

    /** @test */
    public function customer_service_cannot_entry_pod_if_manifest_has_not_been_sent()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = factory(User::class)->states('courier')->create(['network_id' => $network->id]);

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'handler_id'      => $courier->id,
        ]);
        $receipt = factory(Receipt::class)->create();
        $distributionManifest->addReceipt($receipt);

        $this->assertFalse($distributionManifest->isSent());

        $this->visit(route('pods.by-manifest'));
        $this->submitForm(__('manifest.search'), [
            'manifest_number' => $distributionManifest->number,
        ]);
        $this->seePageIs(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));
        $this->see('Belum dapat <strong>Entry POD</strong> karena manifest <strong>Belum Dikirim</strong>.');
        $this->dontSeeElement('a', ['id' => 'pod-entry-'.$receipt->id]);
    }

    /** @test */
    public function customer_service_can_entry_receipt_pod_by_distribution_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = User::find(7); // Seeded Courier user

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'handler_id'      => $courier->id,
            'dest_city_id'    => '6301',
        ]);
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $distributionManifest->addReceipt($receipt);
        $distributionManifest->send();

        $this->visit(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));
        $this->seeElement('a', ['id' => 'pod-entry-'.$receipt->id]);

        $this->assertFalse($distributionManifest->isAllDelivered());
        $this->click('pod-entry-'.$receipt->id);

        $this->seePageIs(route('pods.by-manifest', [
            'manifest_number' => $distributionManifest->number,
            'receipt_number'  => $receipt->number,
        ]));

        $time = Carbon::now()->format('Y-m-d H:i');

        $this->submitForm(__('app.submit'), [
            'delivery_courier_id' => $courier->id,
            'time'                => $time,
            'status_code'         => 'dl',
            'recipient'           => 'Nama Penerima',
            'notes'               => '',
            'customer_invoice_no' => 'NOFAKTUR1234567890',
        ]);

        $this->seePageIs(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));
        $this->see(__('pod.created'));

        $consignee = $receipt->consignee;
        $consignee['recipient'] = 'Nama Penerima';

        $this->seeInDatabase('receipts', [
            'id'                  => $receipt->id,
            'number'              => $receipt->number,
            'consignee'           => json_encode($consignee),
            'status_code'         => 'dl',
            'customer_invoice_no' => 'NOFAKTUR1234567890',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $distributionManifest->id,
            'start_status'        => 'od',
            'creator_id'          => $courier->id,
            'creator_location_id' => $network->origin->id,
            'handler_id'          => $customerService->id,
            'handler_location_id' => $network->origin->id,
            'end_status'          => 'dl',
            'updated_at'          => $time.':00',
        ]);

        $this->seeInDatabase('delivery_proofs', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $distributionManifest->id,
            'courier_id'   => $courier->id,
            'creator_id'   => $customerService->id,
            'location_id'  => $customerService->network->origin->id,
            'status_code'  => 'dl',
            'recipient'    => 'Nama Penerima',
            'delivered_at' => $time.':00',
            'notes'        => null,
        ]);
    }

    /** @test */
    public function customer_service_can_entry_receipt_pod_by_receipt()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = User::find(7); // Seeded Courier user

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'handler_id'      => $courier->id,
            'dest_city_id'    => '6301',
        ]);
        $receipt = factory(Receipt::class)->create([
            'dest_city_id'        => '6301',
            'customer_invoice_no' => 'NOFAKTUR1234567890',
        ]);
        $distributionManifest->addReceipt($receipt);
        $distributionManifest->send();

        $this->visit(route('pods.by-receipt', ['receipt_number' => $receipt->number]));

        $this->seePageIs(route('pods.by-receipt', [
            'receipt_number' => $receipt->number,
        ]));

        $this->seeElement('input', [
            'id'    => 'customer_invoice_no',
            'name'  => 'customer_invoice_no',
            'value' => 'NOFAKTUR1234567890',
        ]);

        $time = Carbon::now()->format('Y-m-d H:i');

        $this->submitForm(__('app.submit'), [
            'delivery_courier_id' => $courier->id,
            'time'                => $time,
            'status_code'         => 'dl',
            'recipient'           => 'Nama Penerima',
            'notes'               => '',
            'customer_invoice_no' => 'NOFAKTUR1234567891',
        ]);

        $this->see(__('pod.created'));
        $this->seePageIs(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));

        $consignee = $receipt->consignee;
        $consignee['recipient'] = 'Nama Penerima';

        $this->seeInDatabase('receipts', [
            'id'                  => $receipt->id,
            'number'              => $receipt->number,
            'consignee'           => json_encode($consignee),
            'status_code'         => 'dl',
            'customer_invoice_no' => 'NOFAKTUR1234567891',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $distributionManifest->id,
            'start_status'        => 'od',
            'creator_id'          => $courier->id,
            'creator_location_id' => $network->origin->id,
            'handler_id'          => $customerService->id,
            'handler_location_id' => $network->origin->id,
            'end_status'          => 'dl',
            'updated_at'          => $time.':00',
        ]);

        $this->seeInDatabase('delivery_proofs', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $distributionManifest->id,
            'courier_id'   => $courier->id,
            'creator_id'   => $customerService->id,
            'location_id'  => $customerService->network->origin->id,
            'status_code'  => 'dl',
            'recipient'    => 'Nama Penerima',
            'delivered_at' => $time.':00',
            'notes'        => null,
        ]);
    }

    /** @test */
    public function inform_user_if_receipt_not_found_on_entry_receipt_pod_by_receipt()
    {
        $this->loginAsCustomerService();
        $this->visitRoute('pods.by-receipt', ['receipt_number' => 'invalid_receipt_number']);

        $this->seeRouteIs('pods.by-receipt', ['receipt_number' => 'invalid_receipt_number']);
        $this->see(__('receipt.not_found'));
    }

    /** @test */
    public function customer_service_edit_entry_receipt_pod()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = factory(User::class)->states('courier')->create(['network_id' => $network->id]);

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'handler_id'      => $courier->id,
            'dest_city_id'    => '6301',
        ]);
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $distributionManifest->addReceipt($receipt);
        $distributionManifest->send();

        $this->visit(route('pods.by-manifest', [
            'manifest_number' => $distributionManifest->number,
            'receipt_number'  => $receipt->number,
        ]));

        $time = Carbon::now()->format('Y-m-d H:i');

        $this->submitForm(__('app.submit'), [
            'delivery_courier_id' => $courier->id,
            'time'                => $time,
            'status_code'         => 'dl',
            'recipient'           => 'Nama Penerima',
            'notes'               => '',
            'customer_invoice_no' => 'NOFAKTUR1234567890',
        ]);

        $this->seePageIs(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));
        $this->see(__('pod.created'));

        $consignee = $receipt->consignee;
        $consignee['recipient'] = 'Nama Penerima';

        $this->seeInDatabase('receipts', [
            'id'                  => $receipt->id,
            'number'              => $receipt->number,
            'consignee'           => json_encode($consignee),
            'status_code'         => 'dl',
            'delivery_courier_id' => $distributionManifest->handler_id,
            'customer_invoice_no' => 'NOFAKTUR1234567890',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $distributionManifest->id,
            'start_status'        => 'od',
            'creator_id'          => $courier->id,
            'creator_location_id' => $network->origin->id,
            'handler_id'          => $customerService->id,
            'handler_location_id' => $network->origin->id,
            'end_status'          => 'dl',
            'updated_at'          => $time.':00',
        ]);

        $this->seeInDatabase('delivery_proofs', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $distributionManifest->id,
            'courier_id'   => $courier->id,
            'creator_id'   => $customerService->id,
            'location_id'  => $customerService->network->origin->id,
            'status_code'  => 'dl',
            'recipient'    => 'Nama Penerima',
            'delivered_at' => $time.':00',
            'notes'        => null,
        ]);

        $receipt = $receipt->fresh();

        $this->visit(route('receipts.pod', $receipt->number));

        $this->seeElement('a', ['href' => $receipt->path().'/pod?action=edit']);
        $this->visit($receipt->path().'/pod?action=edit');

        $time = Carbon::now()->format('Y-m-d H:i');

        $this->submitForm(__('app.update'), [
            'delivery_courier_id' => $receipt->delivery_courier_id,
            'time'                => $time,
            'status_code'         => 'dl',
            'recipient'           => 'Nama Penerima 1',
            'notes'               => 'Catatan dari penerima.',
            'customer_invoice_no' => 'NOFAKTUR1234567891',
        ]);

        $this->see(__('pod.updated'));
        $this->seePageIs($receipt->path().'/pod');

        $consignee = $receipt->consignee;
        $consignee['recipient'] = 'Nama Penerima 1';

        $this->seeInDatabase('receipts', [
            'id'                  => $receipt->id,
            'number'              => $receipt->number,
            'consignee'           => json_encode($consignee),
            'status_code'         => 'dl',
            'customer_invoice_no' => 'NOFAKTUR1234567891',
        ]);

        // $this->seeInDatabase('receipt_progress', [
        //     'receipt_id'          => $receipt->id,
        //     'manifest_id'         => $distributionManifest->id,
        //     'start_status'        => 'od',
        //     'creator_id'          => $courier->id,
        //     'creator_location_id' => $network->origin->id,
        //     'handler_id'          => $customerService->id,
        //     'handler_location_id' => $network->origin->id,
        //     'end_status'          => 'dl',
        //     // 'updated_at'          => $time.':00',
        //     'notes'               => 'Catatan dari penerima.',
        // ]);

        $this->seeInDatabase('delivery_proofs', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $distributionManifest->id,
            'courier_id'   => $courier->id,
            'creator_id'   => $customerService->id,
            'location_id'  => $customerService->network->origin->id,
            'status_code'  => 'dl',
            'recipient'    => 'Nama Penerima 1',
            'delivered_at' => $time.':00',
            'notes'        => 'Catatan dari penerima.',
        ]);
    }

    /** @test */
    public function cannot_entry_receipt_pod_if_receipt_has_no_distribution_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $courier = User::find(7); // Seeded Courier user
        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);

        $this->visit(route('pods.by-receipt', ['receipt_number' => $receipt->number]));

        $this->see(__('pod.cannot_enter_receipt_pod', ['receipt_number' => $receipt->number]));
        $this->seePageIs(route('pods.by-receipt'));
    }

    /** @test */
    public function cannot_entry_receipt_pod_if_receipt_has_no_distribution_manifest_that_on_delivery()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = User::find(7); // Seeded Courier user

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'handler_id'      => $courier->id,
            'dest_city_id'    => '6301',
        ]);

        $receipt = factory(Receipt::class)->create(['dest_city_id' => '6301']);
        $distributionManifest->addReceipt($receipt);

        $this->visit(route('pods.by-receipt', ['receipt_number' => $receipt->number]));

        $this->see(__('pod.cannot_enter_receipt_pod', ['receipt_number' => $receipt->number]));
        $this->seePageIs(route('pods.by-receipt'));
    }

    /** @test */
    public function cannot_entry_receipt_pod_if_receipt_has_delivered()
    {
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;
        $courier = User::find(7); // Seeded Courier user

        $deliveredReceipt = factory(Receipt::class)->create(['dest_city_id' => '6301', 'status_code' => 'dl']);

        $this->visit(route('pods.by-receipt', ['receipt_number' => $deliveredReceipt->number]));

        $this->see(__('pod.cannot_enter_receipt_pod', ['receipt_number' => $deliveredReceipt->number]));
        $this->seePageIs(route('pods.by-receipt'));
    }
}
