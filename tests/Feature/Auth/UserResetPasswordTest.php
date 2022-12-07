<?php

namespace Tests\Feature\Auth;

use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class UserResetPasswordTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function member_can_reset_password_by_their_email()
    {
        $user1 = factory(User::class)->create(['username' => '123456', 'email' => 'member@app.dev']);
        $user2 = factory(User::class)->create(['network_id' => $user1->network_id]);

        // Reset Request
        $this->visit('password/reset');
        $this->notSeeInDatabase('password_resets', [
            'email' => 'member@app.dev'
        ]);
        $this->see(trans('auth.reset_password'));
        $this->type('member@app.dev','email');
        $this->press(trans('auth.send_reset_password_link'));
        $this->seePageIs('password/reset');
        $this->see(trans('passwords.sent'));
        $this->seeInDatabase('password_resets', [
            'email' => 'member@app.dev'
        ]);

        // // Reset Action
        // $resetData = DB::table('password_resets')->where('email','member@app.dev')->first();
        // $token = $resetData->token;

        // $this->visit('password/reset/' . $token);
        // $this->see(trans('auth.reset_password'));
        // $this->see(trans('auth.password_confirmation'));

        // // Enter an invalid email
        // $this->type($user2->email,'email');
        // $this->type('rahasia','password');
        // $this->type('rahasia','password_confirmation');
        // $this->press(trans('auth.reset_password'));
        // $this->see(trans('passwords.token'));

        // // Enter a valid email
        // $this->type('member@app.dev','email');
        // $this->type('rahasia','password');
        // $this->type('rahasia','password_confirmation');
        // $this->press(trans('auth.reset_password'));
        // $this->seePageIs(route('home'));

        // $this->notSeeInDatabase('password_resets', [
        //     'email' => 'member@app.dev'
        // ]);

        // // Logout and login using new Password
        // $this->press('logout-button');
        // $this->seePageIs(route('login'));
        // $this->type('123456','username');
        // $this->type('rahasia','password');
        // $this->press(trans('auth.login'));
        // $this->seePageIs(route('home'));
        // $this->see('Selamat datang kembali ' . $user1->name . '.');
    }

}
