<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;

class HelperFunctionsTest extends TestCase
{
    /** @test */
    public function get_date_array_of_a_year_month()
    {
        $dateArray = \monthDateArray('2017', '01');
        $this->assertCount(31, $dateArray);
        $this->assertEquals(range(1, 31), $dateArray);

        $dateArray = \monthDateArray('2016', '02');
        $this->assertCount(29, $dateArray);
        $this->assertEquals(range(1, 29), $dateArray);
    }
}
