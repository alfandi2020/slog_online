<?php

namespace App\Http\Controllers\Services;

use DB;
use Excel;
use App\Entities\Regions\City;
use App\Entities\Services\Rate;
use App\Entities\Regions\Province;
use App\Entities\Customers\Customer;
use App\Http\Controllers\Controller;
use App\Entities\Services\RatesRepository;

class RateExportsController extends Controller
{
    private $repo;

    public function __construct(RatesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $customers = $this->repo->getCustomersDropdown();
        $originCities = $this->getAvailableOriginCitiesDropdown();
        $destinationProvinces = $this->getDestinationProvincesDropdown();

        return view(
            'services.rates-exports.index',
            compact('originCities', 'customers', 'destinationProvinces')
        );
    }

    public function excel()
    {
        $customerId = request('customer_id');
        $origCityId = request('orig_city_id');
        $originCity = City::findOrFail($origCityId);
        $destinationProvinceId = request('dest_prov_id');
        $province = Province::findOrFail($destinationProvinceId);

        $customer = Customer::find($customerId);
        if (is_null($customer)) {
            $customer = new Customer;
            $customer->id = 0;
            $customer->name = 'Customer Umum';
        }

        $rates = Rate::where('customer_id', request('customer_id'))
            ->where('orig_city_id', $origCityId)
            ->orderBy('orig_city_id')
            ->orderBy('service_id')
            ->get();

        // return view(
        //     'services.rates-exports.excel_province',
        //     compact('rates', 'province', 'originCity', 'customer')
        // );

        Excel::create('export_tarif_'.$customerId.'_'.$origCityId.'_'.$destinationProvinceId, function ($excel) use (
            $rates, $originCity, $customer, $province
        ) {
            $excel->sheet($province->name, function ($sheet) use (
                $rates, $province, $originCity, $customer
            ) {
                $sheet->loadView(
                    'services.rates-exports.excel_province',
                    compact('rates', 'province', 'originCity', 'customer')
                );
            });
        })->export('xls');
    }

    public function base()
    {
        $rates = Rate::where('customer_id', 0)
            ->orderBy('orig_city_id')
            ->orderBy('service_id')
            ->with('cityOrigin', 'districtOrigin', 'cityDestination', 'districtDestination')
            ->get();

        // return view('services.rates-exports.base', compact('rates'));

        Excel::create('Base Rate', function ($excel) use ($rates) {
            $excel->sheet('Base Rate', function ($sheet) use ($rates) {
                $sheet->loadView('services.rates-exports.base', compact('rates'));
            });
        })->export('xls');
    }

    public function customer()
    {
        $rates = Rate::where('customer_id', '>', 0)
            ->orderBy('customer_id')
            ->orderBy('orig_city_id')
            ->orderBy('service_id')
            ->with('customer', 'cityOrigin', 'districtOrigin', 'cityDestination', 'districtDestination')
            ->get();

        // return view('services.rates-exports.customer', compact('rates'));

        Excel::create('Customer Rate', function ($excel) use ($rates) {
            $excel->sheet('Customer Rate', function ($sheet) use ($rates) {
                $sheet->loadView('services.rates-exports.customer', compact('rates'));
            });
        })->export('xls');
    }

    private function getAvailableOriginCitiesDropdown()
    {
        $rateOrigins = DB::table('rates')->select(['orig_city_id'])->distinct()->pluck('orig_city_id');

        return City::whereIn('id', $rateOrigins)->pluck('name', 'id');
    }

    private function getDestinationProvincesDropdown()
    {
        return Province::pluck('name', 'id');
    }
}
