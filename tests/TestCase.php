<?php

namespace Tests;

use App\Entities\Users\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // public function tearDown()
    // {
    //     if (session()->get('test_warning')) {
    //         dump($this->getName(), session()->get('test_warning'));
    //     }
    //     parent::tearDown();
    // }

    protected function loginAsUser($overrides = [])
    {
        $overrides = array_merge(['network_id' => 1], $overrides);
        $user = factory(User::class)->create($overrides);
        $this->actingAs($user);

        return $user;
    }

    protected function loginAsSalesCounter()
    {
        return $this->loginAsUser(['role_id' => 3]);
    }
}
