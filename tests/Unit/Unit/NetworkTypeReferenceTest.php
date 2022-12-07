<?php

namespace Tests\Unit;

use App\Entities\Networks\Type;
use Tests\TestCase;

class NetworkTypeReferenceTest extends TestCase
{
    /** @test */
    public function retrieve_network_types_list()
    {
        $networkType = new Type;

        $this->assertEquals([
            1 => trans('network.province'),
            trans('network.city'),
            trans('network.district'),
            trans('network.outlet'),
        ], $networkType->toArray());
    }

    /** @test */
    public function retrieve_network_type_by_id()
    {
        $networkType = new Type;
        $this->assertEquals(trans('network.province'), $networkType->getNameById(1));
        $this->assertEquals(trans('network.city'), $networkType->getNameById(2));
        $this->assertEquals(trans('network.district'), $networkType->getNameById(3));
        $this->assertEquals(trans('network.outlet'), $networkType->getNameById(4));
    }
}
