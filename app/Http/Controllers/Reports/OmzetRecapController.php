<?php

namespace App\Http\Controllers\Reports;

use App\Entities\Customers\Customer;
use App\Entities\Regions\Province;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class OmzetRecapController extends Controller
{
    public function monthly()
    {
        $year = request('year', date('Y'));
        $month = request('month', date('m'));

        // Invalid Date Check
        if (!isValidDate($year.'-'.$month.'-01')) {
            return redirect()->route('reports.omzet-recap.monthly');
        }

        $monthlyPcBasedOmzetRecap = $this->getMonthlyPcBasedOmzetRecap($year, $month);
        $monthlyWeightBasedOmzetRecap = $this->getMonthlyWeightBasedOmzetRecap($year, $month);
        $customers = $this->getCustomers();
        $cities = $this->getCities();

        return view(
            'reports.recap.monthly',
            compact(
                'monthlyPcBasedOmzetRecap',
                'monthlyWeightBasedOmzetRecap',
                'customers', 'year', 'month', 'cities'
            )
        );
    }

    public function yearly()
    {
        $year = request('year', date('Y'));
        $yearlyPcBasedOmzetRecap = $this->getYearlyPcBasedOmzetRecap($year);
        $yearlyWeightBasedOmzetRecap = $this->getYearlyWeightBasedOmzetRecap($year);
        $customers = $this->getCustomers();
        $cities = $this->getCities();

        return view(
            'reports.recap.yearly',
            compact(
                'yearlyPcBasedOmzetRecap',
                'yearlyWeightBasedOmzetRecap',
                'customers', 'year', 'cities'
            )
        );
    }

    private function getCustomers()
    {
        return Customer::orderBy('account_no')->get();
    }

    private function getCities()
    {
        $provinces = Province::with('cities')->get();

        $cityList = [];

        foreach ($provinces as $province) {
            foreach ($province->cities as $city) {
                $cityList[$province->name][$city->id] = $city->name;
            }
        }

        return $cityList;
    }

    private function getMonthlyPcBasedOmzetRecap($year, $month)
    {
        $rawQuery = "customer_id";
        $rawQuery .= ", count(id) as receipt_total";
        $rawQuery .= ", sum(items_count) as item_total";
        $rawQuery .= ", sum(pcs_count) as pcs_total";
        $rawQuery .= ", sum(weight) as weight_total";
        $rawQuery .= ", sum(bill_amount) as bill_total";

        $omzetQuery = DB::table('receipts')
            ->select(DB::raw($rawQuery))
            ->groupBy('receipts.customer_id')
            ->where('charged_on', 2)
            ->whereNull('deleted_at')
            ->where('pickup_time', 'like', $year.'-'.$month.'%');

        if ($destCityId = request('dest_city_id')) {
            $omzetQuery->where('dest_city_id', $destCityId);

            if ($destDistrictId = request('dest_district_id')) {
                $omzetQuery->where('dest_district_id', $destCityId);
            }
        }

        return $omzetQuery->get();
    }

    private function getMonthlyWeightBasedOmzetRecap($year, $month)
    {
        $rawQuery = "customer_id";
        $rawQuery .= ", count(id) as receipt_total";
        $rawQuery .= ", sum(items_count) as item_total";
        $rawQuery .= ", sum(pcs_count) as pcs_total";
        $rawQuery .= ", sum(weight) as weight_total";
        $rawQuery .= ", sum(bill_amount) as bill_total";

        $omzetQuery = DB::table('receipts')
            ->select(DB::raw($rawQuery))
            ->groupBy('receipts.customer_id')
            ->where('charged_on', 1)
            ->whereNull('deleted_at')
            ->where('pickup_time', 'like', $year.'-'.$month.'%');

        if ($destCityId = request('dest_city_id')) {
            $omzetQuery->where('dest_city_id', $destCityId);

            if ($destDistrictId = request('dest_district_id')) {
                $omzetQuery->where('dest_district_id', $destDistrictId);
            }
        }

        return $omzetQuery->get();
    }

    private function getYearlyPcBasedOmzetRecap($year)
    {
        $rawQuery = "customer_id";
        $rawQuery .= ", count(id) as receipt_total";
        $rawQuery .= ", sum(items_count) as item_total";
        $rawQuery .= ", sum(pcs_count) as pcs_total";
        $rawQuery .= ", sum(weight) as weight_total";
        $rawQuery .= ", sum(bill_amount) as bill_total";

        $omzetQuery = DB::table('receipts')
            ->select(DB::raw($rawQuery))
            ->groupBy('receipts.customer_id')
            ->where('charged_on', 2)
            ->whereNull('deleted_at')
            ->where('pickup_time', 'like', $year.'%');

        if ($destCityId = request('dest_city_id')) {
            $omzetQuery->where('dest_city_id', $destCityId);

            if ($destDistrictId = request('dest_district_id')) {
                $omzetQuery->where('dest_district_id', $destDistrictId);
            }
        }

        return $omzetQuery->get();
    }

    private function getYearlyWeightBasedOmzetRecap($year)
    {
        $rawQuery = "customer_id";
        $rawQuery .= ", count(id) as receipt_total";
        $rawQuery .= ", sum(items_count) as item_total";
        $rawQuery .= ", sum(pcs_count) as pcs_total";
        $rawQuery .= ", sum(weight) as weight_total";
        $rawQuery .= ", sum(bill_amount) as bill_total";

        $omzetQuery = DB::table('receipts')
            ->select(DB::raw($rawQuery))
            ->groupBy('receipts.customer_id')
            ->where('charged_on', 1)
            ->whereNull('deleted_at')
            ->where('pickup_time', 'like', $year.'%');

        if ($destCityId = request('dest_city_id')) {
            $omzetQuery->where('dest_city_id', $destCityId);

            if ($destDistrictId = request('dest_district_id')) {
                $omzetQuery->where('dest_district_id', $destDistrictId);
            }
        }

        return $omzetQuery->get();
    }
}
