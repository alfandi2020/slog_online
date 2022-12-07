<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Entities\Networks\Network;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NetworkTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_network_has_name_link_method()
    {
        $network = factory(Network::class)->make();

        $this->assertEquals(
            link_to_route('admin.networks.show', $network->code.' - '.$network->name, [$network->id], [
                'title' => trans(
                    'app.show_detail_title',
                    ['name' => $network->code.' - '.$network->name, 'type' => trans('network.network')]
                ),
            ]), $network->nameLink()
        );
    }
    /** @test */
    public function a_network_has_code_name_attribute()
    {
        $network = factory(Network::class)->make();

        $this->assertEquals($network->code.' - '.$network->name, $network->code_name);
    }

    /** @test */
    public function a_network_has_full_name_origin_method()
    {
        $network = factory(Network::class)->make();

        $fullOriginName = '';

        if ($network->origin_district_id) {
            $fullOriginName .= $network->districtOrigin->name;
            $fullOriginName .= ' - ';
        }

        $fullOriginName .= $network->cityOrigin->name;
        $fullOriginName .= ' - ';
        $fullOriginName .= $network->cityOrigin->province->name;

        $this->assertEquals($fullOriginName, $network->fullOriginName());
    }

    /** @test */
    public function a_network_has_full_name_origin_method_with_format_parameter()
    {
        $network = factory(Network::class)->make();

        $fullOriginName = '';

        if ($network->origin_district_id) {
            $fullOriginName .= $network->districtOrigin->name;
            $fullOriginName .= '<br>';
        }

        $fullOriginName .= $network->cityOrigin->name;
        $fullOriginName .= '<br>';
        $fullOriginName .= $network->cityOrigin->province->name;

        $this->assertEquals($fullOriginName, $network->fullOriginName('list'));
    }
}
