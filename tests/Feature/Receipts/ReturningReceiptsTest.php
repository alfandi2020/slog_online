<?php

namespace Tests\Feature\Receipts;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReturningReceiptsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_service_can_add_receipt_to_returning_receipts_page()
    {
        $receipt1 = factory(Receipt::class)->create(['status_code' => 'dl']);
        $receipt2 = factory(Receipt::class)->create(['status_code' => 'bd']);

        $this->loginAsCustomerService();
        $this->visit(route('receipts.returnings.index'));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt1->number,
        ]);

        $this->see(trans('manifest.receipt_added'));
        $this->seePageIs(route('receipts.returnings.index'));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt2->number,
        ]);
        $this->seeInSession('receipts.returnings', [
            $receipt1->number,
            $receipt2->number,
        ]);

        $this->see($receipt1->number);
        $this->see($receipt2->number);
    }

    /** @test */
    public function customer_service_cannot_add_non_delivered_receipts_to_returning_receipts_page()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'od']);

        $this->loginAsCustomerService();

        $this->visit(route('receipts.returnings.index'));
        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('receipts.returnings.index'));
        $this->see(trans('manifest.receipt_addition_fails'));
        $this->dontSee($receipt->number);
    }

    /** @test */
    public function customer_service_cannot_add_returing_receipt_of_other_network()
    {
        $receipt = factory(Receipt::class)->create([
            'status_code' => 'dl',
            'network_id'  => 999,
        ]);

        $network = factory(Network::class)->create();
        $customerService = factory(User::class)->create(['role_id' => 5, 'network_id' => $network->id]);
        $this->actingAs($customerService);

        $this->assertNotEquals($customerService->network_id, $receipt->network_id);

        $this->visit(route('receipts.returnings.index'));
        $this->submitForm(__('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('receipts.returnings.index'));
        $this->see(__('receipt.return_entry_fail_network', [
            'receipt' => $receipt->number,
            'network' => $customerService->network->code_name,
        ]));
    }

    /** @test */
    public function bam_kalsel_customer_service_can_add_returing_receipt_of_other_network()
    {
        $receipt = factory(Receipt::class)->create([
            'status_code' => 'dl',
            'network_id'  => 999,
        ]);

        $customerService = $this->loginAsCustomerService();

        $this->assertNotEquals($customerService->network_id, $receipt->network_id);

        $this->visit(route('receipts.returnings.index'));
        $this->submitForm(__('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->see(trans('manifest.receipt_added'));

        $this->seePageIs(route('receipts.returnings.index'));
    }

    /** @test */
    public function customer_service_can_remove_a_receipt_from_returning_list()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'dl']);

        $this->loginAsCustomerService();
        $this->visit(route('receipts.returnings.index'));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seeInSession('receipts.returnings', [
            $receipt->number,
        ]);

        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->seeInSession('receipts.returnings', []);
    }

    /** @test */
    public function customer_service_can_destroy_returning_receipts_list()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'dl']);

        $this->loginAsCustomerService();
        $this->visit(route('receipts.returnings.index'));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seeInSession('receipts.returnings', [
            $receipt->number,
        ]);
        $this->seeElement('button', ['id' => 'destroy-returning-list']);

        $this->press('destroy-returning-list');

        $this->see(trans('receipt.returnings_destroyed'));
        $this->seePageIs(route('receipts.returnings.index'));

        $this->assertSessionMissing('receipts.returnings');
    }

    /** @test */
    public function customer_service_can_receive_returning_receipts()
    {
        $time = date('Y-m-d H:i');
        $customerService = $this->loginAsCustomerService();
        $network = $customerService->network;

        $distributionManifest = factory(Manifest::class, 'distribution')->create([
            'dest_network_id' => $network->id,
            'dest_city_id'    => '6301',
        ]);
        $receipt = factory(Receipt::class)->create([
            'dest_city_id' => '6301',
            'status_code'  => 'de',
        ]);
        $this->assertTrue($distributionManifest->addReceipt($receipt));
        $distributionManifest->send();
        $this->assertTrue($distributionManifest->isSent());

        $receipt->status_code = 'dl';
        $receipt->save();

        $this->loginAsCustomerService();
        $this->visit(route('receipts.returnings.index'));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->submitForm(trans('receipt.set_all_returned'), [
            'returned_time' => $time,
        ]);

        $this->assertSessionMissing('receipts.returnings');
        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'rt',
        ]);
        // dump(\DB::table('receipt_progress')->get());

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $distributionManifest->id,
            'handler_id'  => $customerService->id,
            'end_status'  => 'rt',
            'updated_at'  => $time.':00',
        ]);
    }
}
