<?php

namespace Tests\Unit\Factories;

use App\Entities\Networks\Network;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class UserModelFactoryTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $user = factory(User::class)->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'name' => $user->name,
            'phone' => '081234567890',
            'gender_id' => $user->gender_id,
            'role_id' => $user->role_id,
            'network_id' => $network->id,
            'is_active' => $user->is_active,
        ]);
    }

    /** @test */
    public function admin_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $admin = factory(User::class)->states('admin')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $admin->id,
            'role_id' => 1,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function accounting_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $acounting = factory(User::class)->states('accounting')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $acounting->id,
            'role_id' => 2,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function sales_counter_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $salesCounter = factory(User::class)->states('sales_counter')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $salesCounter->id,
            'role_id' => 3,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function warehouse_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $warehouse = factory(User::class)->states('warehouse')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $warehouse->id,
            'role_id' => 4,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function customer_service_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $customerService = factory(User::class)->states('customer_service')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $customerService->id,
            'role_id' => 5,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function cashier_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $cashier = factory(User::class)->states('cashier')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $cashier->id,
            'role_id' => 6,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function courier_user_factory()
    {
        $network = factory(Network::class)->states('city')->create();
        $courier = factory(User::class)->states('courier')->create(['network_id' => $network->id]);

        $this->seeInDatabase('users', [
            'id' => $courier->id,
            'role_id' => 7,
            'network_id' => $network->id,
        ]);
    }
}
