<?php

namespace Tests\Feature\Receipts;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Services\Rate;
use App\Entities\Receipts\Receipt;
use App\Services\ReceiptCollection;
use App\Entities\Receipts\Drafts\Retail as DraftRetail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DraftRetailReceiptTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sales_counter_can_create_new_retail_receipt_via_get_costs_page()
    {
        $salesCounter = $this->loginAsSalesCounter();

        $rate = factory(Rate::class, 'city_to_city')->create(['orig_city_id' => $salesCounter->network->origin_city_id]);
        $this->visit(route('pages.get-costs'));

        $this->submitForm(trans('service.get_costs'), [
            'orig_city_id'   => $rate->orig_city_id,
            'dest_city_id'   => $rate->dest_city_id,
            'charged_weight' => 2,
        ]);

        $this->see('create-receipt-'.$rate->id);
        $this->press('create-receipt-'.$rate->id);

        $lastReceipt = $this->getReceiptCollection()->content()->last();

        $lastReceiptKey = $lastReceipt->receiptKey;
        $this->seePageIs(route('receipts.draft', $lastReceiptKey));
        $this->assertTrue($lastReceipt instanceof Receipt);
        $this->see(trans('receipt.create_new', ['origin' => $rate->originName(), 'destination' => $rate->destinationName()]));
        $this->visit(route('receipts.draft', [$lastReceiptKey, 'step' => 2]));
        $this->visit(route('receipts.draft', [$lastReceiptKey, 'step' => 3]));
        $this->see(trans('receipt.prevent_review'));
        $this->seePageIs(route('receipts.draft', [$lastReceiptKey, 'step' => 1]));
    }

    /** @test */
    public function draft_retail_receipt_can_be_save_after_review_page()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $networkCode = $salesCounter->network->code;

        factory(Receipt::class)->create(['service_id' => 11, 'number' => $networkCode.'1612000001']);

        $rate = factory(Rate::class, 'city_to_city')->create([
            'orig_city_id' => $salesCounter->network->origin_city_id,
            'rate_kg'      => 10000,
            'rate_pc'      => 10000,
            'service_id'   => 11,
        ]);

        $this->visit(route('pages.get-costs'));

        $this->submitForm(trans('service.get_costs'), [
            'orig_city_id'   => $rate->orig_city_id,
            'dest_city_id'   => $rate->dest_city_id,
            'charged_weight' => 2,
        ]);

        $this->see('create-receipt-'.$rate->id);
        $this->press('create-receipt-'.$rate->id);

        $receiptCollection = $this->getReceiptCollection();

        $lastReceipt = $receiptCollection->content()->last();

        $calculator = (new DraftRetail)->calculateByReceiptQuery($this->getReceiptQuery($rate));
        $lastReceipt = $receiptCollection->updateReceiptData($lastReceipt->receiptKey, $calculator->toArray());

        $this->visit(route('receipts.draft', $lastReceipt->receiptKey));

        $this->submitForm('Submit', [
            'number'              => '',
            'reference_no'        => '1234a',
            'pickup_courier_id'   => 7, // Seeded courier_kalsel
            'customer_invoice_no' => 'NOFAKTUR1234567890',
            'pickup_time'         => '2015-01-01 10:10',
            'payment_type_id'     => 1, // cash
            'pack_content'        => '',
            'notes'               => '',

            'consignor_name'        => 'Testing Pengirim',
            'consignor_address[1]'  => 'Testing alamat Pengirim 1',
            'consignor_address[2]'  => 'Testing alamat Pengirim 2',
            'consignor_address[3]'  => 'Testing alamat Pengirim 3',
            'consignor_postal_code' => '70000',
            'consignor_phone'       => '081234567890',

            'consignee_name'        => 'Testing Penerima',
            'consignee_address[1]'  => 'Testing alamat Penerima 1',
            'consignee_address[2]'  => 'Testing alamat Penerima 2',
            'consignee_address[3]'  => 'Testing alamat Penerima 3',
            'consignee_postal_code' => '70000',
            'consignee_phone'       => '081234567890',
        ]);

        $this->see(trans('receipt.draft_updated'));
        $this->seePageIs(route('receipts.draft', [$lastReceipt->receiptKey, 'step' => 3]));

        $this->press(trans('receipt.save'));
        // $this->seePageIs(route('pages.get-costs'));
        $this->see(trans('receipt.created'));

        $targetedArray = $this->getTargettedArray($rate, $salesCounter);
        $targetedArray['number'] = $networkCode.date('ym').'000001';
        $targetedArray['customer_invoice_no'] = 'NOFAKTUR1234567890';
        $targetedArray['pickup_courier_id'] = 7;

        $this->seeInDatabase('receipts', $targetedArray);
    }

    /** @test */
    public function draft_retail_receipt_can_be_save_with_items_detail()
    {
        $salesCounter = $this->loginAsSalesCounter();

        $rate = factory(Rate::class, 'city_to_city')->create([
            'orig_city_id' => $salesCounter->network->origin_city_id,
            'rate_kg'      => 10000,
            'rate_pc'      => 10000,
        ]);

        $this->visit(route('pages.get-costs'));

        $this->submitForm(trans('service.get_costs'), [
            'orig_city_id'   => $rate->orig_city_id,
            'dest_city_id'   => $rate->dest_city_id,
            'charged_weight' => 2,
        ]);

        $this->see('create-receipt-'.$rate->id);
        $this->press('create-receipt-'.$rate->id);

        $receiptCollection = $this->getReceiptCollection();

        $lastReceipt = $receiptCollection->content()->last();

        $calculator = (new DraftRetail)->calculateByReceiptQuery($this->getReceiptQuery($rate));
        $lastReceipt = $receiptCollection->updateReceiptData($lastReceipt->receiptKey, $calculator->toArray());

        $this->visit(route('receipts.draft', [$lastReceipt->receiptKey]));

        $this->click(trans('receipt.detailing_items'));

        $this->submitForm(trans('receipt.add_item'), [
            'new_item_weight'  => '2',
            'new_item_length'  => '50',
            'new_item_width'   => '30',
            'new_item_height'  => '20',
            'new_item_type_id' => '1',
            'new_item_notes'   => '',
        ]);

        $this->submitForm(trans('receipt.add_item'), [
            'new_item_weight'  => '1',
            'new_item_length'  => '20',
            'new_item_width'   => '30',
            'new_item_height'  => '10',
            'new_item_type_id' => '1',
            'new_item_notes'   => '',
        ]);

        $this->click(trans('app.done'));

        $this->submitForm('Submit', [
            'number'          => '',
            'reference_no'    => '1234a',
            'pickup_time'     => '2015-01-01 10:10',
            'payment_type_id' => 1,
            'pack_content'    => '',
            'notes'           => '',

            'consignor_name'        => 'Testing Pengirim',
            'consignor_address[1]'  => 'Testing alamat Pengirim 1',
            'consignor_address[2]'  => 'Testing alamat Pengirim 2',
            'consignor_address[3]'  => 'Testing alamat Pengirim 3',
            'consignor_postal_code' => '70000',
            'consignor_phone'       => '081234567890',

            'consignee_name'        => 'Testing Penerima',
            'consignee_address[1]'  => 'Testing alamat Penerima 1',
            'consignee_address[2]'  => 'Testing alamat Penerima 2',
            'consignee_address[3]'  => 'Testing alamat Penerima 3',
            'consignee_postal_code' => '70000',
            'consignee_phone'       => '081234567890',
        ]);

        $this->see(trans('receipt.draft_updated'));
        $this->seePageIs(route('receipts.draft', [$lastReceipt->receiptKey, 'step' => 3]));

        $this->press(trans('receipt.save'));
        $this->see(trans('receipt.created'));

        $targetedArray = $this->getTargettedArray($rate, $salesCounter);
        $targetedArray['number'] = $salesCounter->network->code.date('ym').'000001';
        $targetedArray['items_detail'] = '[{"weight":"2","length":"50","width":"30","height":"20","volumetricDevider":6000,"type_id":"1","notes":null,"volume":30000,"volumetric_weight":5,"charged_weight":5,"type":"Paket"},{"weight":"1","length":"20","width":"30","height":"10","volumetricDevider":6000,"type_id":"1","notes":null,"volume":6000,"volumetric_weight":1,"charged_weight":1,"type":"Paket"}]';
        $targetedArray['weight'] = 6;
        $targetedArray['items_count'] = 2;
        $targetedArray['pcs_count'] = 2;
        $targetedArray['amount'] = 60000;
        $targetedArray['bill_amount'] = 60000;
        $targetedArray['costs_detail'] = '{"base_charge":60000,"discount":0,"subtotal":60000,"insurance_cost":0,"packing_cost":0,"add_cost":0,"admin_fee":0,"total":60000}';

        $this->seeInDatabase('receipts', $targetedArray);
    }

    private function getReceiptCollection()
    {
        return new ReceiptCollection;
    }

    private function getReceiptQuery(Rate $rate)
    {
        return [
            'customer_id'      => '',
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'service_id'       => $rate->service_id,
            'pcs_count'        => 1,
            'items_count'      => 1,
            'charged_weight'   => 1,
            'charged_on'       => 2, // 1: weight, 2:item
            'pack_type_id'     => 1,
            'package_value'    => '',
            'be_insured'       => 0,
            'discount'         => 0,
            'packing_cost'     => 0,
            'admin_fee'        => 0,
        ];
    }

    private function getTargettedArray(Rate $rate, User $user)
    {
        return [
            'service_id'       => $rate->service_id,
            'number'           => '1234',
            'pickup_time'      => '2015-01-01 10:10',
            'items_detail'     => null,
            'pcs_count'        => 1,
            'items_count'      => 1,
            'weight'           => 1,
            'pack_type_id'     => 1,
            'pack_content'     => null,
            'pack_value'       => null,
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->orig_district_id,
            'charged_on'       => 2, // 1: weight, 2:item
            'consignor'        => '{"name":"Testing Pengirim","address":{"1":"Testing alamat Pengirim 1","2":"Testing alamat Pengirim 2","3":"Testing alamat Pengirim 3"},"phone":"081234567890","postal_code":"70000"}',
            'consignee'        => '{"name":"Testing Penerima","address":{"1":"Testing alamat Penerima 1","2":"Testing alamat Penerima 2","3":"Testing alamat Penerima 3"},"phone":"081234567890","postal_code":"70000"}',
            'creator_id'       => $user->id,
            'network_id'       => $user->network_id,
            'status_code'      => 'de',
            'invoice_id'       => null,
            'rate_id'          => $rate->id,
            'amount'           => $rate->rate_pc * 1,
            'bill_amount'      => $rate->rate_pc * 1,
            'base_rate'        => $rate->rate_pc,
            'payment_type_id'  => 1,
            'costs_detail'     => '{"base_charge":10000,"discount":0,"subtotal":10000,"insurance_cost":0,"packing_cost":0,"add_cost":0,"admin_fee":0,"total":10000}',
            'customer_id'      => null,
            'reference_no'     => '1234a',
            'notes'            => null,
            'deleted_at'       => null,
        ];
    }

    private function getDraftTargettedArray(Rate $rate)
    {
        return [
            'customer_id'      => null,
            'service_id'       => $rate->service_id,
            'orig_city_id'     => $rate->orig_city_id,
            'orig_district_id' => $rate->orig_district_id,
            'dest_city_id'     => $rate->dest_city_id,
            'dest_district_id' => $rate->dest_district_id,
            'pcs_count'        => 1,
            'items_count'      => 1,
            'charged_weight'   => 1,
            'charged_on'       => 1, // 1: weight, 2:item
            'pack_type_id'     => $rate->pack_type_id,
            'package_value'    => '',
            'be_insured'       => 0,
            'discount'         => 0,
            'packing_cost'     => 0,

            'rate_id'        => $rate->id,
            'insurance_cost' => 0,
            'base_rate'      => $rate->rate_kg,
            'base_charge'    => $rate->rate_kg,
            'subtotal'       => $rate->rate_kg,
            'total'          => $rate->rate_kg,
        ];
    }
}
