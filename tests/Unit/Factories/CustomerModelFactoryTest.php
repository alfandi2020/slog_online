<?php

namespace Tests\Unit\Factories;

use App\Entities\Customers\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use DB;

class CustomerModelFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function customer_factory()
    {
        $customer = factory(Customer::class)->create();
        $this->assertEquals(1, Customer::count());
    }
}
