<?php

namespace Tests\Unit\Policies;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserPolicyTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_create_user()
    {
        $admin = $this->loginAsAdmin();

        $this->assertTrue($admin->can('create', new User));
    }

    /** @test */
    public function admin_can_view_user()
    {
        $admin = $this->loginAsAdmin();
        $user = factory(User::class)->create(['role_id' => 3]);

        $this->assertTrue($admin->can('view', $user));
    }

    /** @test */
    public function admin_can_update_user()
    {
        $admin = $this->loginAsAdmin();
        $user = factory(User::class)->create(['role_id' => 3]);

        $this->assertTrue($admin->can('update', $user));
    }

    /** @test */
    public function admin_cannot_delete_user()
    {
        $admin = $this->loginAsAdmin();
        $user = factory(User::class)->create(['role_id' => 3]);

        $this->assertFalse($admin->can('delete', $user));
    }

    // /** @test */
    // public function admin_cannot_delete_user_that_has_create_receipt()
    // {
    //     $admin = $this->loginAsAdmin();
    //     $user = factory(User::class)->create(['role_id' => 3]);
    //     $receipt = factory(Receipt::class)->create(['creator_id' => $user->id]);

    //     $this->assertFalse($admin->can('delete', $user));
    // }
}
