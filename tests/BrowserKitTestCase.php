<?php

namespace Tests;

use App\Entities\Customers\Customer;
use App\Entities\Invoices\Invoice;
use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;

abstract class BrowserKitTestCase extends \Laravel\BrowserKitTesting\TestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost';

    // public function tearDown()
    // {
    //     if (session()->get('test_warning')) {
    //         dump($this->getName(), session()->get('test_warning'));
    //     }
    //     parent::tearDown();
    // }

    protected function loginAsUser($overrides = [])
    {
        // Using seeded user for login instead of create new user
        $userId = $overrides['role_id'] ?? 1;
        $user = User::findOrFail($userId);
        $this->actingAs($user);

        return $user;
    }

    protected function loginAsAdmin()
    {
        return $this->loginAsUser(['role_id' => 1]);
    }

    protected function loginAsAccounting()
    {
        return $this->loginAsUser(['role_id' => 2]);
    }

    protected function loginAsSalesCounter()
    {
        return $this->loginAsUser(['role_id' => 3]);
    }

    protected function loginAsWarehouse()
    {
        return $this->loginAsUser(['role_id' => 4]);
    }

    protected function loginAsCustomerService()
    {
        return $this->loginAsUser(['role_id' => 5]);
    }

    protected function loginAsCashier()
    {
        return $this->loginAsUser(['role_id' => 6]);
    }

    protected function createInvoiceWithReceipts($accounting, $numberOfReceipts = 1, $overrides = [])
    {
        $customer = factory(Customer::class)->create(['network_id' => $accounting->network_id]);

        $overrides = array_merge([
            'customer_id' => $customer->id,
            'creator_id'  => $accounting->id,
            'network_id'  => $accounting->network_id,
        ], $overrides);

        $invoice = factory(Invoice::class)->create($overrides);

        if ($numberOfReceipts) {
            $invoice->each(function ($invoice) use ($customer, $numberOfReceipts) {
                $invoice->assignReceipt(factory(Receipt::class, $numberOfReceipts)
                        ->states('invoice_ready')
                        ->make(['customer_id' => $customer->id]));
            });
        }

        return $invoice;
    }
}
