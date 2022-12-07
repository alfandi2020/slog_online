<?php

namespace Tests\Feature\Manifests;

use App\Entities\Customers\Customer;
use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class AccountingManifestAddRemoveReceiptsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_service_can_add_credit_receipt_to_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->states('credit')->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create();

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'rt',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'start_status'        => 'ma',
            'creator_id'          => $customerService->id,
            'creator_location_id' => $customerService->network->origin_city_id,
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function customer_service_cannot_add_same_receipt_to_accounting_manifest_twice()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->states('credit')->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create();
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
        $this->see(trans('manifest.receipt_addition_fails'));
    }

    /** @test */
    public function customer_service_cannot_add_cash_or_cod_receipt_to_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $randomStates = array_rand(['cash' => 'cash', 'cod' => 'cod']);
        $receipt = factory(Receipt::class)->states($randomStates)->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create();

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
        $this->see(trans('manifest.accountings.non_credit_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function customer_service_cannot_add_dl_or_bd_receipt_to_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->states('credit')->create(['status_code' => 'dl']);
        $manifest = factory(Manifest::class, 'accounting')->create();

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
        $this->see(trans('manifest.accountings.dl_bd_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function customer_service_cannot_add_receipt_from_other_customer_to_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();

        $manifest = factory(Manifest::class, 'accounting')->create([
            'customer_id' => factory(Customer::class)->create()->id,
        ]);

        $receipt = factory(Receipt::class)->states('credit')->create([
            'status_code' => 'rt', 'customer_id' => $manifest->customer_id,
        ]);

        $otherCustomerReceipt = factory(Receipt::class, 'customer')->states('credit')->create();

        $this->assertNotEquals($receipt->customer_id, $otherCustomerReceipt->customer_id);

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $otherCustomerReceipt->number,
        ]);

        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
        $this->see(trans('manifest.accountings.different_customer_receipt_addition_fails'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $otherCustomerReceipt->id,
            'manifest_id' => $manifest->id,
        ]);
    }

    /** @test */
    public function customer_service_can_remove_receipt_from_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create([
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
        ]);

        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->see(trans('manifest.receipt_removed'));

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $manifest->id,
        ]);

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'rt',
        ]);
    }

    /** @test */
    public function accounting_user_can_reject_receipt_of_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->create(['creator_id' => $customerService->id, 'network_id' => $customerService->network_id, 'status_code' => 'rt']);

        $manifest = factory(Manifest::class, 'accounting')->states('sent')->create([
            'creator_id'      => $customerService->id,
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
        ]);

        $manifest->addReceipt($receipt);

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.receive', $manifest->number));
        $this->submitForm(trans('manifest.reject_receipt'), [
            'receipt_number' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_rejected'));

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'no',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $manifest->id,
            'creator_id'   => $customerService->id,
            'start_status' => 'ma',
            'handler_id'   => $accounting->id,
            'end_status'   => 'no',
        ]);
    }
}
