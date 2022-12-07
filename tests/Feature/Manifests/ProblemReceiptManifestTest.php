<?php

namespace Tests\Feature\Manifests;

use Tests\BrowserKitTestCase;
use App\Entities\Receipts\Receipt;
use App\Entities\Receipts\Progress;
use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\Problem as ProblemManifest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProblemReceiptManifestTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_visit_receipt_problem_manifest_index_page()
    {
        $problemManifest = $this->createProblemManifest(2);

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.problems.index'));
        $this->seePageIs(route('manifests.problems.index'));
        $this->see($problemManifest->number);
    }

    /** @test */
    public function user_can_create_receipt_problem_manifest()
    {
        $accounting = $this->loginAsAccounting();

        // Not OK Receipt by Accounting
        $receipt = factory(Receipt::class)->create(['status_code' => 'no', 'last_officer_id' => $accounting->id]);

        $this->visit(route('manifests.problems.create'));

        $this->select(5, 'handler_id');
        $this->check('receipt_id['.$receipt->id.']');
        $this->type('Catatan Manifest Problem Resi', 'notes');
        $this->press(trans('manifest.problems.create'));

        $this->seeInDatabase('manifests', [
            'number'          => 'M663000000'.date('ym').'00001',
            'type_id'         => 6,
            'weight'          => null,
            'pcs_count'       => null,
            'orig_network_id' => 1,
            'dest_network_id' => 1,
            'creator_id'      => $accounting->id,
            'handler_id'      => 5,
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'notes'               => null,
            'creator_id'          => $accounting->id,
            'creator_location_id' => '6371',
            'start_status'        => 'pr',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function user_can_create_problem_receipt_manifest_based_on_existing_manifest()
    {
        $receipt = factory(Receipt::class)->create(['status_code' => 'no']); // Not OK Receipt

        $manifest = factory(Manifest::class, 'accounting')->states('received')->create([
            'creator_id' => 5, // Seeded CS User
            'handler_id' => 2, // Seeded Accounting User
        ]);

        $progress = Progress::create([
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $manifest->id,
            'creator_id'          => 5, // Seeded CS User
            'creator_location_id' => '6371',
            'start_status'        => 'ma',
            'handler_id'          => 2, // Seeded Accounting User
            'handler_location_id' => '6371',
            'end_status'          => 'no',
        ]);

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.accountings.show', $manifest->number));
        $this->seeElement('a', ['href' => route('manifests.problems.create', ['manifest_number' => $manifest->number])]);
        $this->click(trans('manifest.problems.create'));

        $this->select($manifest->creator_id, 'handler_id');
        $this->check('receipt_id['.$receipt->id.']');
        $this->type('Catatan Manifest Problem Resi', 'notes');
        $this->press(trans('manifest.problems.create'));

        $this->seePageIs(route('manifests.problems.show', 'M663000000'.date('ym').'00001'));

        $this->seeInDatabase('manifests', [
            'number'          => 'M663000000'.date('ym').'00001',
            'type_id'         => 6,
            'weight'          => null,
            'pcs_count'       => null,
            'orig_network_id' => 1,
            'dest_network_id' => 1,
            'creator_id'      => $accounting->id,
            'handler_id'      => $manifest->creator_id,
        ]);

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'no',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'notes'               => null,
            'creator_id'          => $accounting->id,
            'creator_location_id' => $accounting->network->origin->id,
            'start_status'        => 'pr',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function user_can_add_problem_receipt_to_problem_receipt_manifest()
    {
        $problemManifest = $this->createProblemManifest();
        $receipt = factory(Receipt::class)->create(['status_code' => 'no']); // Not OK Receipt

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->click(trans('manifest.add_remove_receipt'));
        $this->seePageIs(route('manifests.problems.show', [$problemManifest->number, 'action' => 'add_remove_receipt']));

        $this->submitForm(trans('manifest.add_receipt'), [
            'receipt_number_a' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.problems.show', [$problemManifest->number, 'action' => 'add_remove_receipt']));

        $this->see(trans('manifest.receipt_added'));

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'no',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $problemManifest->id,
            'start_status'        => 'pr',
            'creator_id'          => $accounting->id,
            'creator_location_id' => '6371',
            'handler_id'          => null,
            'handler_location_id' => null,
            'end_status'          => null,
        ]);
    }

    /** @test */
    public function user_can_remove_receipt_from_problem_receipt_manifest()
    {
        $problemManifest = $this->createProblemManifest(2);
        $this->assertCount(2, $problemManifest->receipts);

        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->click(trans('manifest.add_remove_receipt'));

        $this->submitForm(trans('manifest.remove_receipt'), [
            'receipt_number_r' => $receipt->number,
        ]);

        $this->seePageIs(route('manifests.problems.show', [$problemManifest->number, 'action' => 'add_remove_receipt']));

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'no',
        ]);

        $this->dontSeeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $problemManifest->id,
        ]);

        $this->assertCount(1, $problemManifest->fresh()->receipts);
    }

    /** @test */
    public function user_required_to_fill_reject_reason_to_a_problem_receipt()
    {
        $problemManifest = $this->createProblemManifest(2);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.problems.show', $problemManifest->number));

        $this->seeElement('textarea', ['name' => 'notes['.$receipt->id.']']);

        $this->submitForm(trans('manifest.problems.reject_reason_update'), [
            'notes['.$receipt->id.']' => 'Catatan yang perlu ditindaklanjuti.',
        ]);

        $this->dontSeeElement('textarea', ['name' => 'notes['.$receipt->id.']']);
        $this->see(trans('receipt.problem_notes_updated', ['count' => 2]));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $problemManifest->id,
            'notes'       => 'Catatan yang perlu ditindaklanjuti.',
        ]);
    }

    /** @test */
    public function user_cannot_send_problem_receipt_manifest_if_it_has_any_problem_receipt_with_empty_reason()
    {
        $problemManifest = $this->createProblemManifest(2);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.problems.show', $problemManifest->number));

        $this->seeElement('textarea', ['name' => 'notes['.$receipt->id.']']);

        $this->dontSeeElement('button', ['id' => 'send-manifest']);
    }

    /** @test */
    public function user_can_edit_problem_receipt_reason()
    {
        $problemManifest = $this->createProblemManifest(2, ['reject_reason' => 'Catatan yang perlu ditindaklanjuti.']);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();

        $this->visit(route('manifests.problems.show', $problemManifest->number));

        $this->dontSeeElement('textarea', ['name' => 'notes['.$receipt->id.']']);

        $this->seeElement('a', ['href' => route('manifests.problems.show', [$problemManifest->number, 'action' => 'reason_edit', '#problem-receipt-list'])]);
        $this->click(trans('manifest.problems.edit_reject_reasons'));
        $this->seePageIs(route('manifests.problems.show', [$problemManifest->number, 'action' => 'reason_edit']));

        $this->seeElement('textarea', ['name' => 'notes['.$receipt->id.']']);

        $this->submitForm(trans('manifest.problems.reject_reason_update'), [
            'notes['.$receipt->id.']' => 'Catatan yang perlu ditindaklanjuti.',
        ]);

        $this->seePageIs(route('manifests.problems.show', $problemManifest->number));

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'  => $receipt->id,
            'manifest_id' => $problemManifest->id,
            'notes'       => 'Catatan yang perlu ditindaklanjuti.',
        ]);
    }

    /** @test */
    public function user_can_send_problem_receipt_manifest()
    {
        $problemManifest = $this->createProblemManifest(2, ['reject_reason' => 'Catatan yang perlu ditindaklanjuti.']);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();
        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->dontSeeElement('textarea', ['id' => 'notes['.$receipt->id.']', 'name' => 'notes['.$receipt->id.']']);
        $this->seeElement('button', ['id' => 'send-manifest']);

        $this->press('send-manifest');

        $this->see(trans('manifest.sent'));

        $this->seeInDatabase('receipts', [
            'id'               => $receipt->id,
            'status_code'      => 'pr',
            'last_officer_id'  => $accounting->id,
            'last_location_id' => '6371',
        ]);

        $this->assertNotNull($problemManifest->fresh()->deliver_at);
    }

    /** @test */
    public function user_can_take_a_sent_problem_receipt_back_manifest()
    {
        $problemManifest = $this->createProblemManifest(2, [
            'reject_reason' => 'Catatan yang perlu ditindaklanjuti.',
            'is_sent'       => true,
        ]);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();
        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->seeElement('button', ['id' => 'take-manifest-back']);

        $this->press('take-manifest-back');

        $this->seeInDatabase('receipts', [
            'id'          => $receipt->id,
            'status_code' => 'pr',
        ]);

        $this->seeInDatabase('manifests', [
            'id'         => $problemManifest->id,
            'deliver_at' => null,
        ]);
    }

    /** @test */
    public function user_can_edit_an_unsent_problem_receipt_manifest()
    {
        $problemManifest = $this->createProblemManifest(2);

        $accounting = $this->loginAsAccounting();
        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->seeElement('a', ['href' => route('manifests.problems.edit', $problemManifest->number)]);
        $this->visit(route('manifests.problems.edit', $problemManifest->number));

        $this->submitForm(trans('manifest.update'), [
            'handler_id' => $problemManifest->handler_id,
            'notes'      => 'Mohon segera follow up.',
        ]);

        $this->seeInDatabase('manifests', [
            'id'         => $problemManifest->id,
            'handler_id' => $problemManifest->handler_id,
            'deliver_at' => null,
            'notes'      => 'Mohon segera follow up.',
        ]);
    }

    /** @test */
    public function user_cannot_edit_a_sent_problem_receipt_manifest()
    {
        $problemManifest = $this->createProblemManifest(2, [
            'reject_reason' => 'Catatan yang perlu ditindaklanjuti.',
            'is_sent'       => true,
        ]);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $accounting = $this->loginAsAccounting();
        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->dontSeeElement('a', ['id' => 'edit-manifest', 'href' => route('manifests.problems.edit', $problemManifest->number)]);
        $this->visit(route('manifests.problems.edit', $problemManifest->number));

        $this->seePageIs(route('manifests.problems.show', $problemManifest->number));
        $this->see(trans('manifest.uneditable'));
    }

    /** @test */
    public function user_can_receive_manifest_that_sent_to_them()
    {
        $problemManifest = $this->createProblemManifest(2, [
            'reject_reason' => 'Catatan yang perlu ditindaklanjuti.',
            'is_sent'       => true,
        ]);
        $receipt = $problemManifest->receipts->first(); // Not OK Receipt

        $customerService = $this->loginAsCustomerService();
        $this->visit(route('manifests.problems.show', $problemManifest->number));
        $this->seeElement('button', ['id' => 'receive-manifest']);

        $this->press('receive-manifest');

        $this->seePageIs(route('manifests.problems.show', $problemManifest->number));

        $this->seeInDatabase('manifests', [
            'id'         => $problemManifest->id,
            'handler_id' => $customerService->id,
        ]);

        $problemManifest = $problemManifest->fresh();
        $this->assertNotNull($problemManifest->deliver_at);
        $this->assertNotNull($problemManifest->received_at);

        $this->seeInDatabase('receipts', [
            'id'               => $receipt->id,
            'status_code'      => 'pd',
            'last_officer_id'  => $customerService->id,
            'last_location_id' => '6371',
        ]);

        $this->seeInDatabase('receipt_progress', [
            'receipt_id'          => $receipt->id,
            'manifest_id'         => $problemManifest->id,
            'start_status'        => 'pr',
            'creator_id'          => 2,
            'creator_location_id' => '6371',
            'handler_id'          => $customerService->id,
            'handler_location_id' => '6371',
            'end_status'          => 'pd',
        ]);
    }

    public function createProblemManifest($receiptCount = 1, $options = [])
    {
        $manifestAttribute = ['creator_id' => 2, 'handler_id' => 5];
        $receiptAttribute = ['status_code' => 'no'];

        if (isset($options['is_sent']) && $options['is_sent'] == true) {
            $manifestAttribute['deliver_at'] = date('Y-m-d H:i:s');
            $receiptAttribute['status_code'] = 'pr';
        }

        $manifest = factory(ProblemManifest::class)->create($manifestAttribute);
        $manifest->receipts()
            ->saveMany(
                factory(Receipt::class, $receiptCount)->make($receiptAttribute),
                $this->getProgressAttributes($receiptCount, $options)
            );

        return $manifest;
    }

    public function getProgressAttributes($receiptCount, $options)
    {
        $progressAttributes = [];

        foreach (range(1, $receiptCount) as $count) {
            $progressAttribute = [
                'creator_id'          => 2, // Seeded Accounting User
                'creator_location_id' => '6371',
                'start_status'        => 'pr',
            ];

            if (isset($options['reject_reason'])) {
                $progressAttribute['notes'] = $options['reject_reason'];
            }

            $progressAttributes[] = $progressAttribute;
        }

        return $progressAttributes;
    }
}
