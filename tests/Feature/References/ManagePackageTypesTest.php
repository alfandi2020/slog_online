<?php

namespace Tests\Feature\References;

use App\Entities\References\Reference;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ManagePackageTypesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_create_a_package_type()
    {
        $this->loginAsAdmin();
        $this->visit(route('admin.package-types.index'));

        $this->click(trans('package_type.create'));
        $this->seePageIs(route('admin.package-types.index', ['action' => 'create']));

        $this->type('Paket', 'name');
        $this->press(trans('package_type.create'));

        $this->see(trans('package_type.created'));
        $this->seePageIs(route('admin.package-types.index'));
        $this->see(trans('package_type.created'));

        $this->seeInDatabase('site_references', [
            'name' => 'Paket',
            'cat' => 'pack_type',
        ]);
    }

    /** @test */
    public function admin_can_edit_a_package_type()
    {
        $this->loginAsAdmin();
        $packageType = factory(Reference::class, 'pack_type')->create();

        $this->visit(route('admin.package-types.index'));
        $this->click('edit-package_type-' . $packageType->id);
        $this->seePageIs(route('admin.package-types.index', ['action' => 'edit','id' => $packageType->id]));

        $this->type('Dokumen', 'name');
        $this->press(trans('package_type.update'));

        $this->seeInDatabase('site_references', [
            'name' => 'Dokumen',
            'cat' => 'pack_type',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_package_type()
    {
        $this->loginAsAdmin();
        $packageType = factory(Reference::class, 'pack_type')->create();

        $this->visit(route('admin.package-types.index'));
        $this->click('del-package_type-' . $packageType->id);
        $this->seePageIs(route('admin.package-types.index', ['action' => 'delete','id' => $packageType->id]));

        $this->seeInDatabase('site_references', [
            'id' => $packageType->id
        ]);

        $this->press(trans('app.delete_confirm_button'));

        $this->dontSeeInDatabase('site_references', [
            'id' => $packageType->id
        ]);
    }
}
