<?php

namespace Tests\Unit\Models;

use App\Entities\Receipts\Progress;
use App\Entities\Receipts\Proof;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_progress_has_has_one_proof_relation()
    {
        $progress = factory(Progress::class)->create();
        $proof = factory(Proof::class)->create(['progress_id' => $progress->id]);

        $this->assertInstanceOf(Proof::class, $progress->proof);
        $this->assertEquals($proof->id, $progress->proof->id);
    }
}
