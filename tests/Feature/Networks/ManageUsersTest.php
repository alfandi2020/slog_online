<?php

namespace Tests\Feature\Networks;

use App\Entities\Users\User;
use Tests\BrowserKitTestCase;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageUsersTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_insert_new_user()
    {
        $this->loginAsAdmin();

        $network = factory(Network::class)->create();

        $this->visit(route('admin.users.create'));

        $this->submitForm(trans('user.create'), [
            'username'   => 'namauser',
            'email'      => 'user@mail.com',
            'password'   => 'password',
            'name'       => 'Nama User',
            'phone'      => '01234567890',
            'gender_id'  => 1,
            'role_id'    => 2,
            'network_id' => $network->id,
        ]);
        $this->seePageIs(route('admin.users.index'));
        $this->see(trans('user.created'));
        $this->see('Nama User');
        $this->see('user@mail.com');
        $this->seeInDatabase('users', [
            'username'   => 'namauser',
            'email'      => 'user@mail.com',
            'name'       => 'Nama User',
            'phone'      => '01234567890',
            'gender_id'  => 1,
            'role_id'    => 2,
            'network_id' => $network->id,
        ]);
    }

    /** @test */
    public function admin_can_edit_user_data()
    {
        $this->loginAsAdmin();

        $network1 = factory(Network::class)->states('city')->create();
        $network2 = factory(Network::class)->states('city')->create();

        $user = factory(User::class)->create(['role_id' => 1, 'network_id' => $network1->id, 'gender_id' => 2]);

        $this->visit(route('admin.users.edit', $user->id));
        $this->seePageIs(route('admin.users.edit', $user->id));
        $this->type('Ganti nama User', 'name');
        $this->type('user_name', 'username');
        $this->type('member@mail.dev', 'email');
        $this->select($network2->id, 'network_id');
        $this->select(1, 'gender_id');
        $this->press(trans('user.update'));

        $this->seePageIs(route('admin.users.edit', $user->id));
        $this->see(trans('user.updated'));
        $this->see('Ganti nama User');
        $this->see('user_name');
        $this->see('member@mail.dev');

        $this->seeInDatabase('users', [
            'id'         => $user->id,
            'name'       => 'Ganti nama User',
            'username'   => 'user_name',
            'gender_id'  => 1,
            'email'      => 'member@mail.dev',
            'network_id' => $network2->id,
        ]);
    }

    /** @test */
    public function prevent_admin_from_deleting_users()
    {
        $this->loginAsAdmin();

        $user = factory(User::class)->create(['role_id' => 1]);

        $receipt = factory(Receipt::class)->create(['creator_id' => $user->id]);
        $this->visit(route('admin.users.edit', $user->id));

        $this->dontSeeElement('a', ['id' => 'del-user-'.$user->id]);
        $this->visit(route('admin.users.edit', [$user->id, 'action' => 'delete']));
        $this->dontSeeElement('input', ['value' => trans('app.delete_confirm_button')]);

        // $this->visit(route('admin.users.index'));
        // $this->click('edit-user-'.$user->id);
        // $this->seePageIs(route('admin.users.edit', $user->id));

        // $this->click('del-user-'.$user->id);
        // $this->seePageIs(route('admin.users.edit', [$user->id, 'action' => 'delete']));

        // $this->seeInDatabase('users', [
        //     'id' => $user->id,
        // ]);

        // $this->press(trans('app.delete_confirm_button'));
        // $this->seePageIs(route('admin.users.index'));
        // $this->see(trans('user.deleted'));

        // $this->notSeeInDatabase('users', [
        //     'id' => $user->id,
        // ]);
    }

    // /** @test */
    // public function admin_cannot_deleta_a_user_if_already_in_use()
    // {
    //     $this->loginAsAdmin();

    //     $user = factory(User::class)->create(['role_id' => 1]);
    //     $receipt = factory(Receipt::class)->create(['creator_id' => $user->id]);
    //     $this->visit(route('admin.users.edit', $user->id));

    //     $this->dontSeeElement('a', ['id' => 'del-user-'.$user->id]);
    //     $this->visit(route('admin.users.edit', [$user->id, 'action' => 'delete']));
    //     $this->dontSeeElement('input', ['value' => trans('app.delete_confirm_button')]);
    // }

    /** @test */
    public function admin_can_search_other_users()
    {
        $user = $this->loginAsAdmin();
        $searchedUser = factory(User::class)->create(['name' => 'luzuy']);
        $this->visit(route('admin.users.search'));
        $this->seePageIs(route('admin.users.search'));
        $this->see(trans('user.search'));

        $this->type('luzuy', 'q');
        $this->press(trans('user.search'));

        $this->seePageIs(route('admin.users.search', ['q' => 'luzuy']));
        $this->see('luzuy');
        $this->see($searchedUser->name);
        $this->see($searchedUser->email);
        $this->dontSee($user->email);
    }

    /** @test */
    public function admin_can_see_a_user_profile()
    {
        $user = $this->loginAsAdmin();
        $searchedUser = factory(User::class)->create(['name' => 'luzuy']);

        $this->visit(route('admin.users.show', $searchedUser->id));
        $this->see('luzuy');
        $this->see($searchedUser->name);
        $this->see($searchedUser->email);
        $this->dontSee($user->email);
    }

    /** @test */
    public function admin_can_see_users_on_network_page()
    {
        $user = $this->loginAsAdmin();
        $network = factory(Network::class)->states('city')->create(['code' => '62020000', 'origin_city_id' => '6202']);
        $searchedUser = factory(User::class)->create(['name' => 'luzuy', 'network_id' => $network->id]);

        $this->visit(route('admin.networks.users', $network->id));
        $this->see('luzuy');
        $this->see($searchedUser->name);
        $this->see($searchedUser->email);
        $this->dontSee($user->email);
    }
}
