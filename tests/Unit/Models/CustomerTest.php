<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Entities\Networks\Network;
use App\Entities\Customers\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_customer_has_belongs_to_network_relation()
    {
        $customer = factory(Customer::class)->make();

        $this->assertInstanceOf(Network::class, $customer->network);
        $this->assertEquals($customer->network_id, $customer->network->id);
    }

    /** @test */
    public function a_customer_has_category_attribute()
    {
        $customer = factory(Customer::class)->make();

        $this->assertEquals(__('customer.category_1'), $customer->category);

        $customer->category_id = 2;
        $this->assertEquals(__('customer.category_2'), $customer->category);

        $customer->category_id = 3;
        $this->assertEquals(__('customer.category_3'), $customer->category);
    }

    /** @test */
    public function a_customer_has_pod_checklist_attribute()
    {
        $customer = factory(Customer::class)->make();

        $this->assertEquals(['TTD', 'Nama Penerima', 'Telp.'], $customer->pod_checklist);

        $customer->category_id = 2;
        $this->assertEquals(['TTD', 'Nama Penerima', 'Telp.', 'Stempel'], $customer->pod_checklist);

        $customer->category_id = 3;
        $this->assertEquals(['TTD', 'Nama Penerima', 'Telp.', 'Stempel', 'Faktur/SJ'], $customer->pod_checklist);
    }

    /** @test */
    public function a_customer_has_pod_checklist_display_attribute()
    {
        $customer = factory(Customer::class)->make();

        $this->assertEquals(implode('<br>', array_map(function ($item) {
            return '[ ] '.$item;
        }, $customer->pod_checklist)), $customer->pod_checklist_display);

        $customer->category_id = 2;
        $this->assertEquals(implode('<br>', array_map(function ($item) {
            return '[ ] '.$item;
        }, $customer->pod_checklist)), $customer->pod_checklist_display);

        $customer->category_id = 3;
        $this->assertEquals(implode('<br>', array_map(function ($item) {
            return '[ ] '.$item;
        }, $customer->pod_checklist)), $customer->pod_checklist_display);
    }
}
