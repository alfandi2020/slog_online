<?php

namespace Tests\Unit;

use App\Entities\Users\Role;
use Tests\TestCase;

class UserRoleReferenceTest extends TestCase
{
    /** @test */
    public function retrieve_user_roles_list()
    {
        $userRole = new Role;

        $this->assertEquals([
            1 => trans('user.admin'),
            trans('user.accounting'),
            trans('user.sales_counter'),
            trans('user.warehouse'),
            trans('user.cs'),
            trans('user.cashier'),
            trans('user.courier'),
        ], $userRole->toArray());
    }

    /** @test */
    public function retrieve_user_role_by_id()
    {
        $userRole = new Role;
        $this->assertEquals(trans('user.admin'), $userRole->getById(1));
        $this->assertEquals(trans('user.accounting'), $userRole->getById(2));
        $this->assertEquals(trans('user.sales_counter'), $userRole->getById(3));
        $this->assertEquals(trans('user.warehouse'), $userRole->getById(4));
        $this->assertEquals(trans('user.cs'), $userRole->getById(5));
        $this->assertEquals(trans('user.cashier'), $userRole->getById(6));
        $this->assertEquals(trans('user.courier'), $userRole->getById(7));
    }
}
