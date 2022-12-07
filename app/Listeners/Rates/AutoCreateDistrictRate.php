<?php

namespace App\Listeners\Rates;

use DB;
use App\Events\Rates\Created;
use App\Entities\Services\Rate;

class AutoCreateDistrictRate
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Rates\Created  $event
     * @return void
     */
    public function handle(Created $event)
    {
        $rate = $event->rate;
        $rateDestCityId = $rate->dest_city_id;

        if (!$rate->dest_district_id) {

            $destDistrictsIds = DB::table('districts')->where('city_id', $rateDestCityId)->pluck('id');

            foreach ($destDistrictsIds as $districtId) {
                $districtRate = Rate::firstOrNew([
                    'orig_city_id'     => $rate->orig_city_id,
                    'orig_district_id' => '0',
                    'dest_city_id'     => $rateDestCityId,
                    'dest_district_id' => $districtId,
                    'service_id'       => $rate->service_id,
                    'customer_id'      => $rate->customer_id,
                ]);

                if ($districtRate->exists == false) {
                    $districtRate->rate_kg = $rate->rate_kg;
                    $districtRate->rate_pc = $rate->rate_pc;
                    $districtRate->min_weight = $rate->min_weight;
                    $districtRate->etd = $rate->etd;
                    $districtRate->save();
                }
            }
        }
    }
}
