<?php

namespace Tests\Unit\Models;

use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_has_name_link_method()
    {
        $user = factory(User::class)->make();
        $this->assertEquals(
            link_to_route('admin.users.show', $user->name, [$user->id], [
                'title' => trans(
                    'app.show_detail_title',
                    ['name' => $user->name, 'type' => trans('user.user')]
                ),
            ]), $user->nameLink()
        );
    }

    /** @test */
    public function a_user_has_status_attribute()
    {
        $user = factory(User::class)->make(['is_active' => 1]);
        $this->assertEquals(trans('app.active_status'), $user->status);

        $user = factory(User::class)->make(['is_active' => 0]);
        $this->assertEquals(trans('app.inactive_status'), $user->status);
    }

    /** @test */
    public function a_user_has_gender_attribute()
    {
        $user = factory(User::class)->make(['gender_id' => 1]);
        $this->assertEquals(trans('user.gender_male'), $user->gender);

        $user = factory(User::class)->make(['gender_id' => 2]);
        $this->assertEquals(trans('user.gender_female'), $user->gender);
    }
}
