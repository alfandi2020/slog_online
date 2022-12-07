<?php

namespace App\Transformers;

use App\Entities\Services\ChargeCalculator;
use League\Fractal\TransformerAbstract;

/**
* Cost Transformer
*/
class CostTransformer extends TransformerAbstract
{
    public function transform(ChargeCalculator $calculator)
    {
        $costs = [];

        if ($calculator->rate) {
            $costs[] = [
                'service' => $calculator->getService(),
                'cost' => $calculator->getCharge(),
                'weight' => $calculator->weight,
                'etd' => $calculator->rate->etd,
                'notes' => $calculator->rate->note,
            ];
        }

        return [
            'origin_details' => [
                'id' => '',
                'type' => 'city',
                'name' => $calculator->getOrigin()
            ],
            'destination_details' => [
                'id' => '',
                'type' => 'city',
                'name' => $calculator->getDestination()
            ],
            'costs' => $costs
        ];
    }
}