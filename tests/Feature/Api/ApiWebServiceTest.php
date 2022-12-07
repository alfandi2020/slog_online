<?php

namespace Tests\Feature\Api;

use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ApiWebServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_fetches_receipts_by_receipt_numbers()
    {
        $user = $this->loginAsSalesCounter();
        $receipts = factory(Receipt::class, 2)->create(['creator_id' => $user->id, 'network_id' => $user->network_id]);

        $receiptNumbers = $receipts->pluck('number');
        $response = $this->postJson(route('api.receipts.index'), [
            'receipt_numbers' => $receiptNumbers->implode(',').',12344',
        ], [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json()['data']);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'number',
                    'reference_no',
                    'items_count',
                    'weight',
                    'service',
                    'consignor',
                    'consignee',
                    'origin',
                    'destination',
                    'last_update',
                    'status',
                    'recipient',
                ]
            ]
        ]);
    }

    /** @test */
    public function it_fetches_single_receipt_with_progress()
    {
        $user = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['creator_id' => $user->id, 'network_id' => $user->network_id]);

        $response = $this->getJson(route('api.receipts.show', $receipt->number), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'number',
                'reference_no',
                'service',
                'pickup_time',
                'origin',
                'destination',
                'consignor',
                'consignee',
                'items_count',
                'weight',
                'last_update',
                'status',
                'recipient',
                'is_delivered',
                'progress',
            ]
        ]);
    }

    /** @test */
    public function receipt_detail_only_show_public_progress()
    {
        $user = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class)->create(['creator_id' => $user->id, 'network_id' => $user->network_id, 'dest_city_id' => '6372']);
        $handover = factory(Manifest::class, 'handover')->create();

        $result = $handover->addReceipt($receipt);
        $handover->send();
        $handover->checkReceipt($receipt->number);
        $handover->receive();

        $distribution = factory(Manifest::class, 'distribution')->create(['creator_id' => $user->id, 'dest_city_id' => '6372']);
        $result = $distribution->addReceipt($receipt);

        $response = $this->getJson(route('api.receipts.show', $receipt->number), [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $response->assertStatus(200);
        $this->assertCount(5, $response->json()['data']['progress']);
    }
}
