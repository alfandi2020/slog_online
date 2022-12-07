<?php

namespace Tests\Unit\Models;

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Networks\UnitType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase as TestCase;

class DeliveryUnitTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_type_attribute()
    {
        $deliveryUnit = factory(DeliveryUnit::class)->make();

        $this->assertEquals(
            UnitType::getNameById($deliveryUnit->type_id),
            $deliveryUnit->type
        );
    }
}
