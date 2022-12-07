<?php

namespace Tests\Unit\Policies;

use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptPolicyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_and_accounting_can_recalculate_bill_amount_for_credit_receipt()
    {
        $admin = User::find(1); // Seeded Admin
        $accounting = User::find(2); // Seeded Accounting

        $receipt = factory(Receipt::class)->states('credit')->create([
            'network_id' => 1, // Seeded BAM Kalsel
        ]);
        $this->assertTrue($admin->can('recalculate-bill-amount', $receipt));
        $this->assertTrue($accounting->can('recalculate-bill-amount', $receipt));
    }

    /** @test */
    public function admin_can_recalculate_bill_amount_for_all_receipt()
    {
        $admin = User::find(1); // Seeded Admin
        $receipt = factory(Receipt::class)->states('credit')->create([
            'network_id' => 2, // Receipt of other network
        ]);
        $this->assertTrue($admin->can('recalculate-bill-amount', $receipt));
    }

    /** @test */
    public function accounting_cannot_recalculate_bill_amount_for_other_network_receipts()
    {
        $accounting = User::find(2); // Seeded Accounting

        $unrecalcuatableReceiptByAccounting = factory(Receipt::class)->states('credit')->create([
            'network_id' => 2, // Receipt of other network
        ]);

        $this->assertNotEquals($unrecalcuatableReceiptByAccounting->network_id, $accounting->network_id);
        $this->assertFalse(
            $accounting->can('recalculate-bill-amount', $unrecalcuatableReceiptByAccounting),
            'Accounting user should not able to recalcuate other network receipt bill amount.'
        );
    }

    /** @test */
    public function recalculate_bill_amount_only_for_credit_receipt()
    {
        $admin = User::find(1); // Seeded Admin
        $accounting = User::find(2); // Seeded Accounting

        $unrecalcuatableReceipt = factory(Receipt::class)->states(array_rand(['cash' => 'cash', 'cod' => 'cod']))->create();

        $this->assertFalse(
            $admin->can('recalculate-bill-amount', $unrecalcuatableReceipt),
            'Cannot recalculate bill amount for cash or COD receipt.'
        );

        $this->assertFalse(
            $accounting->can('recalculate-bill-amount', $unrecalcuatableReceipt),
            'Cannot recalculate bill amount for cash or COD receipt.'
        );
    }

    /** @test */
    public function recalculate_bill_amount_only_if_receipt_has_no_base_rate()
    {
        $admin = User::find(1); // Seeded Admin
        $accounting = User::find(2); // Seeded Accounting

        $unrecalcuatableReceipt = factory(Receipt::class)->states('credit')->create(['rate_id' => null, 'service_id' => 41]);

        $this->assertFalse(
            $admin->can('recalculate-bill-amount', $unrecalcuatableReceipt),
            'Cannot recalculate bill amount for receipt that has no base rate.'
        );

        $this->assertFalse(
            $accounting->can('recalculate-bill-amount', $unrecalcuatableReceipt),
            'Cannot recalculate bill amount for receipt that has no base rate.'
        );
    }
}
