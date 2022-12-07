<?php

namespace Tests\Feature\Receipts;

use Tests\BrowserKitTestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Customers\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditReceiptTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function sales_counter_can_edit_receipt_which_has_data_entry_status()
    {
        // Login as Sales Counter or warehouse
        $salesCounter = $this->loginAsSalesCounter();
        // Make new receipt
        $receipt = factory(Receipt::class, 'customer')->create(['creator_id' => $salesCounter]);
        $customer = $receipt->customer;
        // Visit receipt page
        $this->visit($receipt->path());
        // Click edit button
        $this->seeElement('a', ['href' => $receipt->path().'/edit']);
        $this->click(trans('receipt.edit'));
        $this->seePageIs($receipt->path().'/edit');

        // Fill necessary fields and submit
        // isi data resi, pengirim dan penerima
        $this->submitForm(trans('receipt.update'), [
            // Receipt Data
            'customer_id'         => $customer->id,
            // 'number'           => '1234',
            // 'dest_city_id'     => $receipt->dest_city_id,
            // 'dest_district_id' => $receipt->dest_district_id,
            'payment_type_id'     => 1,
            'notes'               => '',
            'reference_no'        => '1234a',
            'pickup_courier_id'   => 7, // Seeded courier_kalsel
            'customer_invoice_no' => 'NOFAKTUR1234567890',

            // Receipt Packag and Cost
            'pickup_time'    => '2015-01-01 10:10',
            'pack_type_id'   => 1,
            'charged_weight' => 1,
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

        // dump(\DB::table('receipts')->get());
        // See notifier that receipt was updeted
        $this->see(trans('receipt.updated'));
        // Redirected to receipt page
        $this->seePageIs($receipt->fresh()->path());
        // See in database that submitted update was applied
        $this->seeInDatabase('receipts', [
            'customer_id'         => $customer->id,
            'number'              => $receipt->number,
            'orig_city_id'        => $receipt->orig_city_id,
            'orig_district_id'    => $receipt->orig_district_id,
            'dest_city_id'        => $receipt->dest_city_id,
            'dest_district_id'    => $receipt->dest_district_id,
            'payment_type_id'     => 1,
            'reference_no'        => '1234a',
            'pickup_courier_id'   => 7, // Seeded courier_kalsel
            'customer_invoice_no' => 'NOFAKTUR1234567890',
            'notes'               => null,
            'pickup_time'         => '2015-01-01 10:10',
            'pack_type_id'        => 1,
            'weight'              => 1,
            'items_count'         => 1,
            'pack_content'        => null,
            'costs_detail'        => json_encode([
                'base_charge'    => 20000,
                'discount'       => 0,
                'subtotal'       => 20000,
                'packing_cost'   => 0,
                'insurance_cost' => 0,
                'add_cost'       => 0,
                'admin_fee'      => 2000,
                'total'          => 22000,
            ]),
            'consignor'           => json_encode([
                'name'        => 'Testing Pengirim',
                'address'     => [1 => 'Testing alamat Pengirim 1', 2 => 'Testing alamat Pengirim 2', 3 => 'Testing alamat Pengirim 3'],
                'postal_code' => '70000',
                'phone'       => '081234567890',
            ]),
            'consignee'           => json_encode([
                'name'        => 'Testing Penerima',
                'address'     => [1 => 'Testing alamat Penerima 1', 2 => 'Testing alamat Penerima 2', 3 => 'Testing alamat Penerima 3'],
                'postal_code' => '70000',
                'phone'       => '081234567890',
            ]),
        ]);
    }

    /** @test */
    public function sales_counter_can_delete_receipt_which_has_data_entry_status()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class, 'customer')->create(['creator_id' => $salesCounter]);

        $this->visit($receipt->path().'/edit');
        $this->seeElement('a', ['href' => $receipt->path().'/delete', 'id' => 'del-receipt-'.$receipt->id]);
        $this->click(trans('receipt.delete'));
        $this->seePageIs($receipt->path().'/delete');

        $this->submitForm(trans('receipt.delete'), [
            'notes' => 'lorem ipsum',
        ]);

        $this->see(trans('receipt.deleted'));
        $this->seePageIs(route('receipts.search'));
        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'deleted_at' => \Carbon\Carbon::now(),
            'notes'      => 'lorem ipsum',
        ]);

        $this->assertEmpty($receipt->all());
        $this->assertNotEmpty($receipt->withTrashed()->get());
    }

    /** @test */
    public function deleted_custom_receipt_number_is_appended_on_deletion()
    {
        $salesCounter = $this->loginAsSalesCounter();
        $receipt = factory(Receipt::class, 'customer')->create(['number' => '1234567890', 'creator_id' => $salesCounter]);
        $oldReceiptNumber = $receipt->number;

        $this->visit($receipt->path().'/edit');
        $this->seeElement('a', ['href' => $receipt->path().'/delete', 'id' => 'del-receipt-'.$receipt->id]);
        $this->click(trans('receipt.delete'));
        $this->seePageIs($receipt->path().'/delete');

        $this->submitForm(trans('receipt.delete'), [
            'notes' => 'lorem ipsum',
        ]);

        $this->see(trans('receipt.deleted'));
        $this->seePageIs(route('receipts.search'));
        $this->seeInDatabase('receipts', [
            'id'         => $receipt->id,
            'deleted_at' => \Carbon\Carbon::now(),
            'notes'      => 'lorem ipsum',
            'number'     => '1234567890_',
        ]);

        $this->assertEmpty($receipt->all());
        $this->assertNotEmpty($receipt->withTrashed()->get());
    }

    /** @test */
    public function admin_or_accounting_user_can_change_receipt_customer_and_payment_method()
    {
        $salesCounter = $this->loginAsAccounting();
        $receipt = factory(Receipt::class)->create(['customer_id' => null, 'payment_type_id' => 1]);
        $customer = factory(Customer::class)->create();

        $this->visit($receipt->path());

        $this->submitForm(trans('receipt.customer_update'), [
            'customer_id'     => $customer->id,
            'payment_type_id' => 2,
        ]);

        $this->seePageIs($receipt->path());
        $this->see(trans('receipt.customer_updated'));

        $this->seeInDatabase('receipts', [
            'id'              => $receipt->id,
            'customer_id'     => $customer->id,
            'payment_type_id' => 2,
        ]);
    }
}
