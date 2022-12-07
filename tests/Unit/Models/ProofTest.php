<?php

namespace Tests\Unit\Models;

use App\Entities\Receipts\Progress;
use App\Entities\Receipts\Proof;
use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProofTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_proof_has_belongs_to_progress_relation()
    {
        $progress = factory(Progress::class)->create();
        $proof = factory(Proof::class)->create(['progress_id' => $progress->id]);

        $this->assertInstanceOf(Progress::class, $proof->progress);
        $this->assertEquals($progress->id, $proof->progress->id);
    }

    /** @test */
    public function a_proof_has_belongs_to_courier_relation()
    {
        $proof = factory(Proof::class)->make();

        $this->assertInstanceOf(User::class, $proof->courier);
        $this->assertEquals($proof->courier_id, $proof->courier->id);
    }

    /** @test */
    public function a_proof_has_belongs_to_creator_relation()
    {
        $proof = factory(Proof::class)->make();

        $this->assertInstanceOf(User::class, $proof->creator);
        $this->assertEquals($proof->creator_id, $proof->creator->id);
    }

    /** @test */
    public function a_proof_has_belongs_to_location_relation()
    {
        $proof = factory(Proof::class)->make();

        $this->assertInstanceOf(City::class, $proof->location);
        $this->assertEquals($proof->location_id, $proof->location->id);

        $proof = factory(Proof::class)->make(['location_id' => '6371010']);

        $this->assertInstanceOf(District::class, $proof->location);
    }
}
