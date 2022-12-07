<?php

namespace App\Entities\Services;

use App\Entities\BaseRepository;
use App\Entities\Regions\Province;

/**
 * Rates Repository Class
 */
class RatesRepository extends BaseRepository
{
    protected $model;
    protected $_paginate = 25;

    public function __construct(Rate $model)
    {
        parent::__construct($model);
    }

    public function getRatesList(array $rateFilter)
    {
        return Rate::where(function ($query) use ($rateFilter) {
            if (isset($rateFilter['orig_city_id']) && $rateFilter['orig_city_id']) {
                $query->where('orig_city_id', $rateFilter['orig_city_id']);
            }

            if (isset($rateFilter['dest_city_id']) && $rateFilter['dest_city_id']) {
                $query->where('dest_city_id', $rateFilter['dest_city_id']);
            }

        })
            ->with('cityOrigin', 'districtOrigin', 'cityDestination', 'districtDestination')
            ->paginate(25);
    }

    public function getRegionsList()
    {
        return [
            3 => 'Jawa',
            5 => 'Balinusra',
            1 => 'Sumatera',
            6 => 'Kalimantan',
            7 => 'Sulawesi',
            8 => 'Maluku',
            9 => 'Papua',
        ];
    }

    public function getRegionProvincesList($region)
    {
        if (in_array($region, [1, 3, 4, 5, 6, 7, 8, 9]) == false) {
            return [];
        }

        switch ($region) {
            case 1:return Province::where('id', 'like', '1%')->orWhere('id', 'like', '2%')->get();
                break;
            case 3:return Province::where('id', 'like', '3%')->get();
                break;
            case 5:return Province::where('id', 'like', '5%')->get();
                break;
            case 6:return Province::where('id', 'like', '6%')->get();
                break;
            case 7:return Province::where('id', 'like', '7%')->get();
                break;
            case 8:return Province::where('id', 'like', '8%')->get();
                break;
            case 9:return Province::where('id', 'like', '9%')->get();
                break;
            default:return Province::where('id', 'like', '6%')->get();
                break;
        }
    }

    public function updateRateData($ratesData)
    {
        extract($ratesData);
        $updatedCount = 0;
        foreach ($rate as $destId => $value) {
            if (strlen($destId) == 7) {
                $rate = $this->model->firstOrNew([
                    'orig_city_id'     => $orig_city_id,
                    'orig_district_id' => '0',
                    'dest_city_id'     => substr($destId, 0, 4),
                    'dest_district_id' => $destId,
                    'service_id'       => $service_id,
                    'customer_id'      => $customer_id,
                ]);
            } else {
                $rate = $this->model->firstOrNew([
                    'orig_city_id'     => $orig_city_id,
                    'orig_district_id' => '0',
                    'dest_city_id'     => $destId,
                    'dest_district_id' => '0',
                    'service_id'       => $service_id,
                    'customer_id'      => $customer_id,
                ]);
            }
            if ($value['kg'] || $value['pc']) {
                $rate->rate_kg = $value['kg'];
                $rate->rate_pc = $value['pc'];
                $rate->min_weight = $value['min_weight'];
                $rate->etd = $value['etd'];
                $rate->save();
                event(new \App\Events\Rates\Created($rate));
                $updatedCount++;
            } elseif (!$value['kg'] && !$value['pc'] && !$value['min_weight'] && !$value['etd']) {
                if ($rate->exists && $rate->dest_district_id) {
                    $rate->delete();
                }
            }
        }
        return $updatedCount;
    }
}
