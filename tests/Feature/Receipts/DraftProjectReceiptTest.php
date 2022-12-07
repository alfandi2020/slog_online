<?php

namespace Tests\Feature\Receipts;

use App\Entities\Customers\Customer;
use App\Services\ReceiptCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class DraftProjectReceiptTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sales_counter_can_make_project_receipt()
    {
        // Login sebagai Sales Counter/Warehouse
        $salesCounter = $this->loginAsSalesCounter();
        $customer = factory(Customer::class)->create(['network_id' => $salesCounter->network_id]);

        $this->visit(route('receipts.drafts'));
        // Tekan tombol Buat Resi Borongan
        $this->press(trans('receipt.41.create'));
        // Isi item resi
        $lastReceipt = $this->getReceiptCollection()->content()->last();
        $this->assertEquals('Borongan', $lastReceipt->type);

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
            'new_item_width'   => '20',
            'new_item_height'  => '20',
            'new_item_type_id' => '1',
            'new_item_notes'   => '',
        ]);

        $this->click(trans('receipt.project_data_entry'));
        $this->seePageIs(route('receipts.draft', [$lastReceipt->receiptKey, 'step' => 2]));

        // isi data resi, pengirim dan penerima
        $this->submitForm(trans('receipt.submit_and_review'), [
            // Receipt Data
            'customer_id'         => $customer->id,
            'number'              => '1234',
            'orig_city_id'        => 6371,
            'orig_district_id'    => null,
            'dest_city_id'        => 6271,
            'dest_district_id'    => null,
            'payment_type_id'     => 1,
            'notes'               => '',
            'reference_no'        => '1234a',
            'pickup_courier_id'   => 7, // Seeded courier_kalsel
            'customer_invoice_no' => 'NOFAKTUR1234567890',

            // Receipt Packag and Cost
            'pickup_time'    => '2015-01-01 10:10',
            'pack_type_id'   => 1,
            'charged_weight' => 1,
            'pcs_count'      => 2,
            'items_count'    => 1,
            'pack_content'   => '',
            'base_charge'    => 20000,
            'discount'       => 0,
            'packing_cost'   => 0,
            'insurance_cost' => 0,
            'add_cost'       => 0,
            'admin_fee'      => 2000,

            // Receipt Consignor & Consignee
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

        // isi data pembayaran resi (rate_id = null)
        // review
        $this->see('Kota Banjarmasin');
        $this->see('Kota Palangkaraya');
        $this->see(formatRp(20000));
        $this->see(formatRp(2000));
        $this->see(formatRp(0));
        $this->see(formatRp(2000));

        // Simpan ke database
        $this->press(trans('receipt.save'));
        $this->see(trans('receipt.created'));

        // Cek database
        $targettedReceiptRecordOverrides['customer_invoice_no'] = 'NOFAKTUR1234567890';
        $targettedReceiptRecordOverrides['customer_id'] = $customer->id;
        $targettedReceiptRecordOverrides['pickup_courier_id'] = 7;

        $this->seeInDatabase('receipts', $this->targettedReceiptRecord($salesCounter, $targettedReceiptRecordOverrides));
    }

    /** @test */
    public function sales_counter_can_make_project_receipt_for_retail_customer()
    {
        // Login sebagai Sales Counter/Warehouse
        $salesCounter = $this->loginAsSalesCounter();
        // $customer = factory(Customer::class)->create(['network_id' => $salesCounter->network_id]);

        $this->visit(route('receipts.drafts'));
        // Tekan tombol Buat Resi Borongan
        $this->press(trans('receipt.41.create'));
        // Isi item resi
        $lastReceipt = $this->getReceiptCollection()->content()->last();
        $this->assertEquals('Borongan', $lastReceipt->type);

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
            'new_item_width'   => '20',
            'new_item_height'  => '20',
            'new_item_type_id' => '1',
            'new_item_notes'   => '',
        ]);

        $this->click(trans('receipt.project_data_entry'));
        $this->seePageIs(route('receipts.draft', [$lastReceipt->receiptKey, 'step' => 2]));

        // isi data resi, pengirim dan penerima
        $this->submitForm(trans('receipt.submit_and_review'), [
            // Receipt Data
            'customer_id'      => '',
            'number'           => '1234',
            'orig_city_id'     => 6371,
            'orig_district_id' => null,
            'dest_city_id'     => 6271,
            'dest_district_id' => null,
            'payment_type_id'  => 1,
            'notes'            => '',
            'reference_no'     => '1234a',

            // Receipt Packag and Cost
            'pickup_time'    => '2015-01-01 10:10',
            'pack_type_id'   => 1,
            'charged_weight' => 1,
            'pcs_count'      => 2,
            'items_count'    => 1,
            'pack_content'   => '',
            'base_charge'    => 20000,
            'discount'       => 0,
            'packing_cost'   => 0,
            'insurance_cost' => 0,
            'add_cost'       => 0,
            'admin_fee'      => 2000,

            // Receipt Consignor & Consignee
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

        // isi data pembayaran resi (rate_id = null)
        // review
        $this->see('Kota Banjarmasin');
        $this->see('Kota Palangkaraya');
        $this->see(formatRp(20000));
        $this->see(formatRp(2000));
        $this->see(formatRp(0));
        $this->see(formatRp(2000));

        // Simpan ke database
        $this->press(trans('receipt.save'));
        $this->see(trans('receipt.created'));

        // Cek database
        $this->seeInDatabase('receipts', $this->targettedReceiptRecord($salesCounter));
    }

    public function targettedReceiptRecord($salesCounter, $overrides = [])
    {
        return array_merge([
            'service_id'       => 41,
            'number'           => '1234',
            'pickup_time'      => '2015-01-01 10:10:00',
            'items_detail'     => '[{"weight":"2","length":"50","width":"30","height":"20","volumetricDevider":4000,"type_id":"1","notes":null,"volume":30000,"volumetric_weight":7.5,"charged_weight":7.5,"type":"Paket"},{"weight":"1","length":"20","width":"20","height":"20","volumetricDevider":4000,"type_id":"1","notes":null,"volume":8000,"volumetric_weight":2,"charged_weight":2,"type":"Paket"}]',
            'pcs_count'        => 2,
            'items_count'      => 1,
            'weight'           => 1,
            'pack_type_id'     => 1,
            'pack_content'     => null,
            'pack_value'       => null,
            'orig_city_id'     => 6371,
            'orig_district_id' => 0,
            'dest_city_id'     => 6271,
            'dest_district_id' => 0,
            'charged_on'       => 1, // 1: weight, 2:item
            'consignor'        => '{"name":"Testing Pengirim","address":{"1":"Testing alamat Pengirim 1","2":"Testing alamat Pengirim 2","3":"Testing alamat Pengirim 3"},"phone":"081234567890","postal_code":"70000"}',
            'consignee'        => '{"name":"Testing Penerima","address":{"1":"Testing alamat Penerima 1","2":"Testing alamat Penerima 2","3":"Testing alamat Penerima 3"},"phone":"081234567890","postal_code":"70000"}',
            'creator_id'       => $salesCounter->id,
            'network_id'       => $salesCounter->network_id,
            'status_code'      => 'de',
            'invoice_id'       => null,
            'rate_id'          => null,
            'amount'           => 22000,
            'bill_amount'      => 22000,
            'base_rate'        => 0,
            'payment_type_id'  => 1,
            'costs_detail'     => '{"base_charge":20000,"discount":0,"subtotal":20000,"insurance_cost":0,"packing_cost":0,"add_cost":0,"admin_fee":2000,"total":22000}',
            'customer_id'      => null,
            'reference_no'     => '1234a',
            'notes'            => null,
            'deleted_at'       => null,
        ], $overrides);
    }

    private function getReceiptCollection()
    {
        return new ReceiptCollection;
    }
}
