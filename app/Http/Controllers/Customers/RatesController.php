<?php

namespace App\Http\Controllers\Customers;

use App\Entities\Customers\Customer;
use App\Entities\Services\Rate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Customer $customer)
    {
        $rates = $customer->rates()->with([
            'cityOrigin', 'districtOrigin', 'cityDestination', 'districtDestination',
        ])->get();

        return view('customers.rates.index')->with([
            'customer' => $customer,
            'rates'    => $rates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Customer $customer)
    {
        return view('customers.rates.create', compact('customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Customer $customer)
    {
        $this->validate($request, [
            'orig_city_id'     => 'required|numeric',
            'orig_district_id' => 'nullable|numeric',
            'dest_city_id'     => 'required|numeric',
            'dest_district_id' => 'nullable|numeric',
            'service_id'       => 'required|numeric',
            'rate_kg'          => 'required_without:rate_pc|nullable|numeric',
            'rate_pc'          => 'required_without:rate_kg|nullable|numeric',
            'min_weight'       => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'add_cost'         => 'nullable|numeric',
            'pack_type_id'     => 'required|numeric',
            'etd'              => 'required|string|max:10',
            'notes'            => 'nullable|numeric|max:255',
        ], [
            'rate_kg.required_without' => 'Wajib diisi salah satu.',
            'rate_pc.required_without' => 'Wajib diisi salah satu.',
        ]);

        $rateData = $this->proccessRequestData($request);
        $rateData['customer_id'] = $customer->id;
        $rate = Rate::create($rateData);

        flash(trans('rate.created'), 'success');
        return redirect()->route('customers.rates.index', $customer->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer, Rate $rate)
    {
        dd($customer, $rate);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer, Rate $rate)
    {
        return view('customers.rates.edit', compact('customer', 'rate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer, Rate $rate)
    {
        $this->validate($request, [
            'rate_kg'      => 'nullable|required_without:rate_pc|numeric',
            'rate_pc'      => 'nullable|required_without:rate_kg|numeric',
            'min_weight'   => 'required|numeric',
            'discount'     => 'nullable|numeric',
            'add_cost'     => 'nullable|numeric',
            'pack_type_id' => 'required|numeric',
            'etd'          => 'required|string|max:10',
            'notes'        => 'nullable|numeric|max:255',
        ]);

        $rateData = $request->only(
            'rate_kg', 'rate_pc', 'min_weight',
            'discount', 'add_cost', 'pack_type_id', 'etd', 'notes'
        );

        foreach ($rateData as $key => $value) {
            if (!$rateData[$key]) {
                $rateData[$key] = null;
            }

        }

        $rate->update($rateData);

        flash(trans('rate.updated'), 'success');
        return redirect()->route('customers.rates.edit', [$customer->id, $rate->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Customer $customer, Rate $rate)
    {
        // TODO: rate_destroy validation based on rate_id that doesnt exist in receipts table
        $this->validate($request, [
            // 'rate_id' => 'required|exists:rates,id|not_exists:receipts,rate_id'
            'rate_id' => 'required|exists:rates,id',
        ]);

        if ($request->get('rate_id') == $rate->id && $rate->delete()) {
            flash(trans('rate.deleted'), 'success');
            return redirect()->route('customers.rates.index', $customer->id);
        }

        flash(trans('rate.undeleted'), 'error');
        return back();
    }

    public function proccessRequestData(Request $request)
    {
        $data = $request->only(
            'service_id', 'orig_city_id', 'orig_district_id',
            'dest_city_id', 'dest_district_id', 'rate_kg', 'rate_pc',
            'min_weight', 'discount', 'add_cost', 'pack_type_id', 'etd', 'notes'
        );

        if (!$data['orig_district_id']) {
            $data['orig_district_id'] = 0;
        }

        if (!$data['dest_district_id']) {
            $data['dest_district_id'] = 0;
        }

        // foreach ($data as $key => $value) {
        //     // if (!$data[$key]) $data[$key] = null;
        // }

        return $data;
    }
}
