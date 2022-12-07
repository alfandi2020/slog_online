<?php

namespace Tests\Feature\Receipts;

use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ReceivingDistributionManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_service_can_receive_distribution_manifest_after_pod_entries_was_completed()
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

        $this->submitForm(trans('app.submit'), [
            'delivery_courier_id' => $courier->id,
            'time'                => $time,
            'status_code'         => 'dl',
            'recipient'           => 'Nama Penerima',
            'notes'               => '',
            'customer_invoice_no' => 'NOFAKTUR1234567890',
        ]);

        $this->seePageIs(route('pods.by-manifest', ['manifest_number' => $distributionManifest->number]));
        $this->see(trans('pod.created'));

        $distributionManifest = $distributionManifest->fresh();
        $this->assertTrue($distributionManifest->isAllDelivered());

        $this->click(trans('manifest.receive'));
        $this->seePageIs(route('pods.by-manifest', [
            'action'          => 'receive',
            'manifest_number' => $distributionManifest->number,
        ]));

        $this->type($time, 'received_at');
        $this->type('10000', 'start_km');
        $this->type('10300', 'end_km');
        $this->type('1234', 'notes');
        $this->press(trans('manifest.receive'));
        $this->seePageIs(route('pods.by-manifest', [
            'manifest_number' => $distributionManifest->number,
        ]));
        $this->see(trans('manifest.received'));

        $distributionManifest = $distributionManifest->fresh();
        $this->assertTrue($distributionManifest->isReceived());
        // $this->assertTrue($receipt->pod instanceof ReceiptProgress);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $distributionManifest->id,
            'start_status'        => 'od',
            'creator_id'          => $courier->id,
            'creator_location_id' => $network->origin->id,
            'handler_id'          => $customerService->id,
            'handler_location_id' => $network->origin->id,
            'end_status'          => 'rt',
        ]);
    }
}
