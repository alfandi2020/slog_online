<?php

namespace Tests\Feature\Auth;

use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class UserLoginTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_validates_the_login_form()
    {
        $this->visit(route('login'))
            ->type('foobar', 'username')
            ->type('secret', 'password')
            ->press(trans('auth.login'))
            ->dontSeeIsAuthenticated()
            ->seePageIs(route('login'));
        $this->see(trans('auth.failed'));
    }

    /** @test */
    public function user_can_login()
    {
        $user = factory(User::class)->create(['password' => '123456']);

        $this->visit(route('login'));
        $this->type($user->username, 'username');
        $this->type('123456', 'password');
        $this->press(trans('auth.login'));

        $this->seePageIs(route('home'));
        $this->see(trans('auth.welcome', ['name' => $user->name]));

        $this->click('logout-button');
        $this->seePageIs(route('login'));
    }

    /** @test */
    public function it_can_logout_of_the_application()
    {
        $user = factory(User::class)->create(['password' => '123456']);
        $this->actingAs($user)
            ->visit(route('home'))
            ->click('logout-button')
            ->seePageis(route('login'))
            ->dontSeeIsAuthenticated();
    }

    /** @test */
    public function user_cannot_login_if_they_account_is_not_active()
    {
        $user = factory(User::class)->create(['password' => '123456', 'is_active' => 0]);

        $this->visit(route('login'));

        $this->submitForm(trans('auth.login'), [
            'username' => $user->username,
            'password' => '123456',
        ]);

        $this->see(trans('auth.user_inactive'));
        $this->seePageIs(route('login'));
    }
}
