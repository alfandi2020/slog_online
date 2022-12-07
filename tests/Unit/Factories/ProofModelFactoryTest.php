<?php

namespace Tests\Unit\Factories;

use App\Entities\Receipts\Proof;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProofModelFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function proof_factory()
    {
        factory(Proof::class)->create();
        $this->assertEquals(1, Proof::count());
    }
}
