<?php

namespace Tests\Feature\Api;

use App\Entities\Regions\Province;
use App\Entities\Services\Rate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ApiGetCostsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function retrieve_city_to_city_costs()
    {
        $user = $this->loginAsUser();
        $rate1 = factory(Rate::class, 'city_to_city')->create(['service_id' => 11, 'rate_kg' => 10000]);
        $rate2 = factory(Rate::class, 'city_to_city')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
        ]);

        $this->postJson(route('api.get-costs'), [
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'weight' => 2,
        ], [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeStatusCode(200);

        $this->seeJson([
            'id' => $rate1->orig_city_id,
            'id' => $rate1->dest_city_id,
            'weight' => 2,
            'cost' => 20000,
            'cost' => 40000,
        ]);

        $this->dontSeeJson([
            'type' => 'district'
        ]);

        // $this->seeJsonStructure($this->getJsonStructure());
    }

    /** @test */
    public function retrieve_city_to_district_costs()
    {
        $user = $this->loginAsUser();
        $rate1 = factory(Rate::class, 'city_to_district')->create(['service_id' => 11, 'rate_kg' => 10000]);
        $rate2 = factory(Rate::class, 'city_to_district')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'dest_district_id' => $rate1->dest_district_id,
        ]);

        $this->postJson(route('api.get-costs'), [
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'dest_district_id' => $rate1->dest_district_id,
            'weight' => 2,
        ], [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeStatusCode(200);

        $this->seeJson([
            'id' => $rate1->orig_city_id,
            'id' => $rate1->dest_district_id,
            'type' => 'city',
            'type' => 'district',
            'weight' => 2,
            'cost' => 20000,
            'cost' => 40000,
        ]);

        // $this->seeJsonStructure($this->getJsonStructure());
    }

    /** @test */
    public function retrieve_district_to_city_costs()
    {
        $user = $this->loginAsUser();
        $rate1 = factory(Rate::class, 'district_to_city')->create(['service_id' => 11, 'rate_kg' => 10000]);
        $rate2 = factory(Rate::class, 'district_to_city')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'orig_district_id' => $rate1->orig_district_id,
            'dest_city_id' => $rate1->dest_city_id,
        ]);

        $this->postJson(route('api.get-costs'), [
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'orig_district_id' => $rate1->orig_district_id,
            'weight' => 2,
        ], [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeStatusCode(200);

        $this->seeJson([
            'id' => $rate1->orig_district_id,
            'id' => $rate1->dest_city_id,
            'type' => 'city',
            'type' => 'district',
            'weight' => 2,
            'cost' => 20000,
            'cost' => 40000,
        ]);

        // $this->seeJsonStructure($this->getJsonStructure());
    }

    /** @test */
    public function retrieve_district_to_district_costs()
    {
        $user = $this->loginAsUser();
        $rate1 = factory(Rate::class, 'district_to_district')->create(['service_id' => 11, 'rate_kg' => 10000]);
        $rate2 = factory(Rate::class, 'district_to_district')->create([
            'service_id' => 12, 'rate_kg' => 20000,
            'orig_city_id' => $rate1->orig_city_id,
            'orig_district_id' => $rate1->orig_district_id,
            'dest_city_id' => $rate1->dest_city_id,
            'dest_district_id' => $rate1->dest_district_id,
        ]);

        $this->postJson(route('api.get-costs'), [
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'orig_district_id' => $rate1->orig_district_id,
            'dest_district_id' => $rate1->dest_district_id,
            'weight' => 2,
        ], [
            'Authorization' => 'Bearer ' . $user->api_token
        ]);

        $this->seeStatusCode(200);

        $this->seeJson([
            'id' => $rate1->orig_district_id,
            'id' => $rate1->dest_district_id,
            'type' => 'district',
            'weight' => 2,
            'cost' => 20000,
            'cost' => 40000,
        ]);

        $this->dontSeeJson([
            'type' => 'city'
        ]);

        // $this->seeJsonStructure($this->getJsonStructure());
    }

    public function getJsonStructure()
    {
        return [
            'origin_details' => [
                'id',
                'type',
                'name'
            ],
            'destination_details' => [
                'id',
                'type',
                'name'
            ],
            'costs' => [
                '*' => [
                    'city_origin',
                    'district_origin',
                    'city_destination',
                    'district_destination',
                    'service',
                    'cost',
                    'weight',
                    'etd',
                    'notes',
                ]
            ]
        ];
    }
}
