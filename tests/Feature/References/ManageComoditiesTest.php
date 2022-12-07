<?php

namespace Tests\Feature\References;

use App\Entities\Customers\Customer;
use App\Entities\References\Reference;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ManageComoditiesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_create_a_comodity()
    {
        $this->loginAsAdmin();
        $this->visit(route('admin.comodities.index'));

        $this->click(trans('comodity.create'));
        $this->seePageIs(route('admin.comodities.index', ['action' => 'create']));

        $this->type('Comodoty 1', 'name');
        $this->press(trans('comodity.create'));

        $this->seePageIs(route('admin.comodities.index'));
        $this->see(trans('comodity.created'));

        $this->seeInDatabase('site_references', [
            'name' => 'Comodoty 1',
            'cat' => 'comodity',
        ]);
    }

    /** @test */
    public function admin_can_edit_a_comodity()
    {
        $this->loginAsAdmin();
        $comodity = factory(Reference::class, 'comodity')->create();

        $this->visit(route('admin.comodities.index'));
        $this->click('edit-comodity-' . $comodity->id);
        $this->seePageIs(route('admin.comodities.index', ['action' => 'edit','id' => $comodity->id]));

        $this->type('Comodoty 1', 'name');
        $this->press(trans('comodity.update'));

        $this->seeInDatabase('site_references', [
            'name' => 'Comodoty 1',
            'cat' => 'comodity',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_comodity()
    {
        $this->loginAsAdmin();
        $comodity = factory(Reference::class, 'comodity')->create();

        $this->visit(route('admin.comodities.index'));
        $this->click('del-comodity-' . $comodity->id);
        $this->seePageIs(route('admin.comodities.index', ['action' => 'delete','id' => $comodity->id]));

        $this->seeInDatabase('site_references', [
            'id' => $comodity->id
        ]);

        $this->press(trans('app.delete_confirm_button'));

        $this->dontSeeInDatabase('site_references', [
            'id' => $comodity->id
        ]);
    }

    /** @test */
    public function admin_can_not_delete_a_comodity_that_has_customer()
    {
        $this->loginAsAdmin();
        $customer = factory(Customer::class)->create();
        $comodityId = $customer->comodity_id;

        $this->visit(route('admin.comodities.index'));
        $this->click('del-comodity-' . $comodityId);
        $this->seePageIs(route('admin.comodities.index', ['action' => 'delete','id' => $comodityId]));

        $this->press(trans('app.delete_confirm_button'));

        $this->see(trans('comodity.undeleted'));
        $this->seePageIs(route('admin.comodities.index', ['action' => 'delete','id' => $comodityId]));

        $this->seeInDatabase('site_references', [
            'id' => $comodityId
        ]);
    }
}
