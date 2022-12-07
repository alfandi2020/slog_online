<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CostsCalculator;
use App\Transformers\CostsTransformer;
use Illuminate\Http\Request;

class CostsController extends Controller
{
    public function getCosts(Request $request, CostsCalculator $calculator)
    {
        $this->validate($request, [
            'orig_city_id' => 'required|numeric',
            'dest_city_id' => 'required|numeric',
            'orig_district_id' => 'numeric',
            'dest_district_id' => 'numeric',
            'weight' => 'required|numeric',
        ]);

        if ($request->has('orig_district_id') && $request->has('dest_district_id')) {
            $calculator->calculate(
                $request->get('orig_district_id'),
                $request->get('dest_district_id'),
                $request->get('weight')
            );
        } else if ($request->has('dest_district_id')) {
            $calculator->calculate(
                $request->get('orig_city_id'),
                $request->get('dest_district_id'),
                $request->get('weight')
            );
        } else if ($request->has('orig_district_id')) {
            $calculator->calculate(
                $request->get('orig_district_id'),
                $request->get('dest_city_id'),
                $request->get('weight')
            );
        } else {
            $calculator->calculate(
                $request->get('orig_city_id'),
                $request->get('dest_city_id'),
                $request->get('weight')
            );
        }

        $response = fractal()
            ->item($calculator)
            ->transformWith(new CostsTransformer)
            ->toArray();

        return $response['data'];
    }
}
