<?php

namespace App\Http\Controllers\Reports;

use DB;
use App\Entities\Customers\Customer;
use App\Http\Controllers\Controller;

class TimeSeriesController extends Controller
{
    public function omzet()
    {
        $year = request('year', date('Y'));
        $omzetTimeSeriesRecap = $this->getOmzetTimeSeriesRecap($year);
        $customerIds = $omzetTimeSeriesRecap->pluck('customer_id')->unique()->all();
        $customers = $this->getCustomersBasedOnRecap($customerIds);

        return view('reports.time_series.omzet', compact(
            'omzetTimeSeriesRecap',
            'customers', 'year'
        ));
    }

    private function getOmzetTimeSeriesRecap($year)
    {
        $rawQuery = "customer_id";
        $rawQuery .= ", payment_type_id";
        $rawQuery .= ", sum(bill_amount) as bill_total";
        $rawQuery .= ", month(pickup_time) as month";
        $rawQuery .= ", year(pickup_time) as year";

        $recapQuery = DB::table('receipts')
            ->select(DB::raw($rawQuery))
            ->groupBy('receipts.customer_id')
            ->groupBy(DB::raw('month'))
            ->groupBy(DB::raw('year'))
            ->groupBy('payment_type_id')
            ->whereNull('deleted_at')
            ->where('pickup_time', 'like', $year.'%');

        return $recapQuery->get();
    }

    public function invoice()
    {
        $year = request('year', date('Y'));
        $invoiceTimeSeriesRecap = $this->getInvoiceTimeSeriesRecap($year);
        $customerIds = $invoiceTimeSeriesRecap->pluck('customer_id')->unique()->all();
        $customers = $this->getCustomersBasedOnRecap($customerIds);

        return view('reports.time_series.invoice', compact(
            'invoiceTimeSeriesRecap',
            'customers', 'year'
        ));
    }

    public function closedInvoice()
    {
        $year = request('year', date('Y'));
        $invoiceTimeSeriesRecap = $this->getInvoiceTimeSeriesRecap($year, 'verify_date', 'closed');
        $customerIds = $invoiceTimeSeriesRecap->pluck('customer_id')->unique()->all();
        $customers = $this->getCustomersBasedOnRecap($customerIds);

        return view('reports.time_series.closed_invoice', compact(
            'invoiceTimeSeriesRecap',
            'customers', 'year'
        ));
    }

    private function getInvoiceTimeSeriesRecap($year, $referenceDate = 'date', $status = null)
    {
        $rawQuery = "customer_id";
        $rawQuery .= ", type_id";
        $rawQuery .= ", sum(amount) as amount";
        $rawQuery .= ", month({$referenceDate}) as month";
        $rawQuery .= ", year({$referenceDate}) as year";

        $recapQuery = DB::table('invoices')
            ->select(DB::raw($rawQuery))
            ->groupBy('invoices.customer_id')
            ->groupBy(DB::raw('month'))
            ->groupBy(DB::raw('year'))
            ->groupBy('type_id')
            ->where($referenceDate, 'like', $year.'%');

        if ($status == 'closed') {
            $recapQuery->whereNotNull('verify_date');
        }

        return $recapQuery->get();
    }

    private function getCustomersBasedOnRecap(array $customerIds)
    {
        return Customer::orderBy('account_no')->whereIn('id', $customerIds)->get();
    }
}
