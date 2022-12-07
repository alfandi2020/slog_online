<?php

namespace Tests\Unit\Factories;

use App\Entities\References\Reference;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ReferenceModelFactoryTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function comodity_factory()
    {
        $comodity = factory(Reference::class, 'comodity')->create();

        $this->seeInDatabase('site_references', [
            'id' => $comodity->id,
            'cat' => 'comodity',
            'name' => $comodity->name,
        ]);
        $this->assertCount(0, $comodity->customers);
    }

    /** @test */
    public function pack_type_factory()
    {
        $packType = factory(Reference::class, 'pack_type')->create();

        $this->seeInDatabase('site_references', [
            'id' => $packType->id,
            'cat' => 'pack_type',
            'name' => $packType->name,
        ]);
    }
}
