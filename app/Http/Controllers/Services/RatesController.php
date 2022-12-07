<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Request;
use App\Entities\Services\Rate;
use App\Http\Controllers\Controller;
use App\Entities\Services\RatesRepository;

class RatesController extends Controller
{
    private $repo;

    public function __construct(RatesRepository $repo)
    {
        $this->repo = $repo;
    }

    function list(Request $request) {
        $rates = $this->repo->getRatesList($request->only(['orig_city_id', 'dest_city_id']));
        return view('services.rates.list', compact('rates'));
    }

    public function create()
    {
        return view('services.rates.create');
    }

    public function listStore(Request $request)
    {
        $this->validate($request, [
            'service_id'       => 'required|numeric',
            'orig_city_id'     => 'required|numeric',
            'orig_district_id' => 'nullable|numeric',
            'dest_city_id'     => 'required|numeric',
            'dest_district_id' => 'nullable|numeric',
            'rate_kg'          => 'required|numeric',
            'rate_pc'          => 'nullable|numeric',
            'etd'              => 'required|string|max:10',
            'notes'            => 'nullable|string|max:255',
        ]);

        $data = $this->proccessRequestData($request);

        try {
            $rate = Rate::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            // Catch QueryException for duplicated database unique value
            flash('Tarif dasar dari Origin dan Destination ini sudah ada.', 'danger');
            return back()->withInput();
        }

        flash(trans('rate.created'), 'success');

        return redirect()->route('rates.list');
    }

    public function edit(Rate $rate)
    {
        return view('services.rates.edit', compact('rate'));
    }

    public function update(Request $request, Rate $rate)
    {
        $this->validate($request, [
            'rate_kg' => 'required|numeric',
            'rate_pc' => 'nullable|numeric',
            'etd'     => 'required|string|max:10',
            'notes'   => 'nullable|string|max:255',
        ]);

        $data = $request->only(
            'rate_kg', 'rate_pc', 'etd', 'notes'
        );

        $rate->update($data);

        flash(trans('rate.updated'), 'success');

        return redirect()->route('rates.edit', $rate->id);
    }

    public function delete(Rate $rate)
    {
        return view('services.rates.delete', compact('rate'));
    }

    public function destroy(Request $request, Rate $rate)
    {
        // TODO: rate_destroy validation based on rate_id that doesnt exist in receipts table
        $this->validate($request, [
            // 'rate_id' => 'required|exists:rates,id|not_exists:receipts,rate_id'
            'rate_id' => 'required|exists:rates,id',
        ]);

        if ($request->get('rate_id') == $rate->id && $rate->delete()) {
            flash(trans('rate.deleted'), 'success');
            return redirect()->route('rates.list');
        }

        flash(trans('rate.undeleted'), 'error');
        return back();
    }

    public function proccessRequestData(Request $request)
    {
        $data = $request->only(
            'service_id', 'orig_city_id', 'orig_district_id',
            'dest_city_id', 'dest_district_id',
            'rate_kg', 'rate_pc', 'etd', 'notes'
        );

        foreach ($data as $key => $value) {
            if (!$data[$key]) {
                $data[$key] = null;
            }

            if (!$data['orig_district_id']) {
                $data['orig_district_id'] = 0;
            }

            if (!$data['dest_district_id']) {
                $data['dest_district_id'] = 0;
            }

        }

        return $data;
    }

    public function index(Request $request)
    {
        $destCity = null;
        $regions = $this->repo->getRegionsList();
        $region = $request->get('region', 3);
        $provinceList = $this->repo->getRegionProvincesList($region);
        $origCityId = $request->get('orig_city_id', auth()->user()->network->origin_city_id);
        $serviceId = $request->get('service_id', 11);
        $customerId = $request->get('customer_id', 0);
        $customers = $this->repo->getCustomersDropdown();
        if ($request->has('dest_city_id')) {
            $destCity = $this->repo->getCityById($request->get('dest_city_id'));
        }

        return view('services.rates.index', compact('regions', 'regionId', 'provinceList', 'origCityId', 'serviceId', 'destCity', 'customerId', 'customers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'orig_city_id'      => 'required|numeric',
            'service_id'        => 'required|numeric',
            'customer_id'       => 'required|numeric',
            'rate.*.kg'         => 'nullable|numeric',
            'rate.*.pc'         => 'nullable|numeric',
            'rate.*.min_weight' => 'required_with:rate.*.kg,rate.*.pc',
            'rate.*.etd'        => 'required_with:rate.*.kg,rate.*.pc',
        ], [
            'rate.*.kg.numeric'               => 'Harus berupa Angka.',
            'rate.*.pc.numeric'               => 'Harus berupa Angka.',
            'rate.*.min_weight.required_with' => 'Wajib diisi.',
            'rate.*.etd.required_with'        => 'Wajib diisi.',
        ]);
        $updatedCount = $this->repo->updateRateData($request->only('orig_city_id', 'service_id', 'rate', 'customer_id'));

        flash($updatedCount.' '.trans('rate.updated'), 'success');
        return back();
    }
}
