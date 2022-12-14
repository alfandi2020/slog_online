<?php

namespace Tests\Feature\Auth;

use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class UserChangePasswordTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_change_password()
    {
        $user = factory(User::class)->make(['username' => '123456']);
        $this->actingAs($user);
        $this->visit(route('change-password'));

        $this->type('secret','old_password');
        $this->type('member','password');
        $this->type('member','password_confirmation');
        $this->press(trans('auth.change_password'));
        $this->see(trans('auth.old_password_success'));
        $this->seePageIs(route('change-password'));

        $this->assertTrue(app('hash')->check('member', $user->password));
    }
}
