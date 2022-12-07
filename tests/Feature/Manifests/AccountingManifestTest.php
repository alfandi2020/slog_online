<?php

namespace Tests\Feature\Manifests;

use App\Entities\Customers\Customer;
use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Receipt;
use App\Events\Manifests\AccountingReceived;
use App\Events\Manifests\AccountingSent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class AccountingManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_service_can_make_an_accounting_manifest_to_accounting()
    {
        $customerService = $this->loginAsCustomerService();
        $customer = factory(Customer::class)->create(['network_id' => $customerService->network_id]);

        $this->visit(route('manifests.accountings.create'));
        $this->select($customer->id, 'customer_id');
        $this->type('', 'notes');
        $this->press(trans('manifest.accountings.create'));

        $this->see(trans('manifest.created'));
        $this->see(trans('manifest.receipt_lists'));

        $manifestNumber = 'M563000000'.date('ym').'00001';
        $this->seePageIs(route('manifests.accountings.show', $manifestNumber));

        $this->seeInDatabase('manifests', [
            'number'           => $manifestNumber,
            'type_id'          => 5,
            'weight'           => null,
            'pcs_count'        => null,
            'orig_network_id'  => $customerService->network_id,
            'dest_network_id'  => $customerService->network_id,
            'creator_id'       => $customerService->id,
            'handler_id'       => null,
            'customer_id'      => $customer->id,
            'delivery_unit_id' => null,
            'start_km'         => null,
            'end_km'           => null,
            'deliver_at'       => null,
            'received_at'      => null,
            'notes'            => null,
        ]);
    }

    /** @test */
    public function customer_service_can_delete_an_unsent_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $manifest = factory(Manifest::class, 'accounting')->create([
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
        ]);
        $receipt = factory(Receipt::class)->create();
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.accountings.show', $manifest->number));

        $this->click(trans('manifest.edit'));
        $this->seePageIs(route('manifests.accountings.edit', $manifest->number));
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
    public function a_sent_accounting_manifest_cannot_be_delete_or_edit()
    {
        $customerService = $this->loginAsCustomerService();
        $manifest = factory(Manifest::class, 'accounting')->create([
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
        ]);
        $receipt = factory(Receipt::class)->create(['status_code' => 'rt']);
        $manifest->addReceipt($receipt);
        // $this->expectsEvents(AccountingSent::class);
        $manifest->send();

        $this->visit(route('manifests.accountings.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
    }

    /** @test */
    public function an_accounting_manifest_can_only_be_sent_out_if_it_has_receipts()
    {
        $customerService = $this->loginAsCustomerService();
        $receipt = factory(Receipt::class)->create(['status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->create([
            'creator_id'      => $customerService->id,
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
        ]);
        $manifest->addReceipt($receipt);

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->see($receipt->number);
        $this->see('<span class="label label-default">On Proccess</span>');
        $this->dontSee('<span class="label label-info">On Delivery</span>');

        $this->expectsEvents(AccountingSent::class);
        $this->press(trans('manifest.send'));
        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
        $this->see(trans('manifest.sent'));

        $manifest = $manifest->fresh();

        $this->assertTrue($manifest->isSent());
        $this->assertFalse($manifest->isReceived());
        $this->see('<span class="label label-info">On Delivery</span>');
        $this->dontSee('<span class="label label-default">On Proccess</span>');
        $this->dontSeeElement('input', ['value' => trans('manifest.add_receipt')]);
        $this->dontSeeElement('input', ['value' => trans('manifest.remove_receipt')]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => $customerService->id,
            'creator_location_id' => $customerService->network->origin_city_id,
            'start_status'        => 'ma',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'ma',
        ]);
    }

    /** @test */
    public function a_sent_accounting_manifest_can_be_receive_by_accounting_user()
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
        $this->submitForm(trans('manifest.check_receipt'), [
            'receipt_number' => $receipt->number,
        ]);
        $this->see(trans('manifest.receipt_pass'));

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'ir',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'   => $receipt->id,
            'manifest_id'  => $manifest->id,
            'creator_id'   => $customerService->id,
            'start_status' => 'ma',
            'handler_id'   => $accounting->id,
            'end_status'   => 'ir',
        ]);

        $this->expectsEvents(AccountingReceived::class);
        $this->press(trans('manifest.receive'));
        $this->see('<span class="label label-success">Received</span>');
        $this->see(trans('manifest.received'));
    }

    /** @test */
    public function customer_service_can_edit_an_unsent_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $customer = factory(Customer::class)->create(['network_id' => $customerService->network_id]);
        $manifest = factory(Manifest::class, 'accounting')->create([
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
        ]);

        $receipt = factory(Receipt::class)->create(['status_code' => 'dl']);
        $manifest->addReceipt($receipt);

        // Visit manifest detail and SEE Edit Button
        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->seeElement('a', ['href' => route('manifests.accountings.edit', $manifest->number)]);

        $this->visit(route('manifests.accountings.edit', $manifest->number));
        $this->seePageIs(route('manifests.accountings.edit', $manifest->number));

        $this->submitForm(trans('manifest.update'), [
            'customer_id' => $customer->id,
            'notes'       => 'Lorem ipsum dolor ismet.',
        ]);

        $this->seePageIs(route('manifests.accountings.show', $manifest->number));

        $this->seeInDatabase('manifests', [
            'number'      => $manifest->number,
            'type_id'     => 5,
            'customer_id' => $customer->id,
            'notes'       => 'Lorem ipsum dolor ismet.',
        ]);
    }

    /** @test */
    public function customer_service_cannot_edit_a_sent_accounting_manifest()
    {
        $customerService = $this->loginAsCustomerService();
        $manifest = factory(Manifest::class, 'accounting')->create([
            'orig_network_id' => $customerService->network_id,
            'dest_network_id' => $customerService->network_id,
            'creator_id'      => $customerService->id,
        ]);

        $receipt = factory(Receipt::class)->create(['status_code' => 'dl']);
        $manifest->addReceipt($receipt);
        $this->expectsEvents(AccountingSent::class);
        $manifest->send();

        // Visit manifest detail and NO Edit Button
        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->dontSeeElement('a', ['href' => route('manifests.accountings.edit', $manifest->number)]);

        // Visit manifest edit page and redirected to manifest detail and see warning
        $this->visit(route('manifests.accountings.edit', $manifest->number));
        $this->see(trans('manifest.uneditable'));
        $this->seePageIs(route('manifests.accountings.show', $manifest->number));
    }
}
