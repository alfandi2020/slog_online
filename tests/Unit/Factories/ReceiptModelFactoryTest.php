<?php

namespace Tests\Unit\Factories;

use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ReceiptModelFactoryTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function receipt_factory()
    {
        $receipt = factory(Receipt::class)->create();

        $this->seeInDatabase('receipts', [
            'service_id'       => $receipt->service_id,
            'number'           => $receipt->number,
            'pickup_time'      => $receipt->pickup_time,
            'items_detail'     => null,
            'pcs_count'        => $receipt->pcs_count,
            'items_count'      => $receipt->items_count,
            'weight'           => $receipt->weight,
            'pack_content'     => null,
            'pack_value'       => $receipt->pack_value,
            'orig_city_id'     => $receipt->orig_city_id,
            'orig_district_id' => $receipt->orig_district_id,
            'dest_city_id'     => $receipt->dest_city_id,
            'dest_district_id' => $receipt->dest_district_id,
            'charged_on'       => 1,
            'consignor'        => json_encode($receipt->consignor),
            'consignee'        => json_encode($receipt->consignee),
            'creator_id'       => $receipt->creator_id,
            'network_id'       => $receipt->network_id,
            'status_code'      => 'de',
            'invoice_id'       => null,
            'rate_id'          => $receipt->rate_id,
            'amount'           => $receipt->amount,
            'bill_amount'      => $receipt->bill_amount,
            'base_rate'        => $receipt->base_rate,
            'reference_no'     => null,
            'payment_type_id'  => $receipt->payment_type_id,
            'customer_id'      => null,
            'pack_type_id'     => $receipt->pack_type_id,
            'costs_detail'     => json_encode($receipt->costs_detail),
            'notes'            => $receipt->notes,
            'deleted_at'       => null,
        ]);
    }
    /** @test */
    public function customer_receipt_factory()
    {
        $receipt = factory(Receipt::class, 'customer')->make();
        $this->assertNotNull($receipt->customer_id);
    }
}
