<?php

namespace App\Transformers;

use App\Services\CostsCalculator;
use League\Fractal\TransformerAbstract;

/**
* Costs Transformer
*/
class CostsTransformer extends TransformerAbstract
{
    public function transform(CostsCalculator $calculator)
    {
        $costs = [];

        if ($rates = $calculator->getRates()) {
            foreach ($rates as $rate) {
                $costs[] = [
                    'city_origin' => $rate->cityOrigin->name,
                    'district_origin' => $rate->districtOrigin ? $rate->districtOrigin->name : null,
                    'city_destination' => $rate->cityDestination->name,
                    'district_destination' => $rate->districtDestination ? $rate->districtDestination->name : null,
                    'service' => $rate->service(),
                    'cost' => $rate->cost,
                    'weight' => $calculator->weight,
                    'etd' => $rate->etd,
                    'notes' => $rate->notes,
                ];
            }
        }

        $originIdLen = strlen($calculator->originId);
        $destinationIdLen = strlen($calculator->destinationId);

        return [
            'origin_details' => [
                'id' => $calculator->originId,
                'type' => $originIdLen == 4 ? 'city' : 'district',
                'name' => $calculator->getOrigin()
            ],
            'destination_details' => [
                'id' => $calculator->destinationId,
                'type' => $destinationIdLen == 4 ? 'city' : 'district',
                'name' => $calculator->getDestination()
            ],
            'costs' => $costs
        ];
    }
}