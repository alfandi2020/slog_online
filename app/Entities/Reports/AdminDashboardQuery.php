<?php

namespace App\Entities\Reports;
use App\Entities\Receipts\Receipt;
use App\Entities\Receipts\Status;
use Cache;
use Carbon\Carbon;
use DB;

/**
* Admin Dashboard Query Class
*/
class AdminDashboardQuery
{
    public static function receiptPaymentMonitor($ym, $paymentType)
    {
        $receiptMonitorData = [];

        $querySelect = "count(id) as receipt_total, sum(bill_amount) as bill_total, network_id";
        $receiptMonitorData['all'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', $paymentType)
            ->whereNull('deleted_at')
            ->groupBy('network_id')
            ->get();

        $querySelect = "count(id) as receipt_total, sum(bill_amount) as bill_total, network_id";
        $receiptMonitorData['uninvoiced'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', $paymentType)
            ->whereNull('deleted_at')
            ->whereNull('invoice_id')
            ->groupBy('network_id')
            ->get();

        $querySelect = "count(receipts.id) as receipt_total, sum(receipts.bill_amount) as bill_total, receipts.network_id";
        $receiptMonitorData['invoiced'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->leftJoin('invoices', 'invoices.id', 'receipts.invoice_id')
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', $paymentType)
            ->whereNull('deleted_at')
            ->whereNotNull('invoice_id')
            ->whereNull('invoices.verify_date')
            ->groupBy('network_id')
            ->get();

        $querySelect = "count(receipts.id) as receipt_total, sum(receipts.bill_amount) as bill_total, receipts.network_id";
        $receiptMonitorData['paid'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->leftJoin('invoices', 'invoices.id', 'receipts.invoice_id')
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', $paymentType)
            ->whereNull('deleted_at')
            ->whereNotNull('invoice_id')
            ->whereNotNull('invoices.verify_date')
            ->groupBy('network_id')
            ->get();

        return $receiptMonitorData;
    }

    public static function receiptMonitor($ym)
    {
        $receiptMonitorData = [];

        $querySelect = "count(id) as receipt_total, sum(bill_amount) as bill_total, network_id";
        $receiptMonitorData['all'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->whereNull('deleted_at')
            ->groupBy('network_id')
            ->get();

        $querySelect = "count(id) as receipt_total, sum(bill_amount) as bill_total, network_id";
        $receiptMonitorData['cash'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', 1)
            ->whereNull('deleted_at')
            ->groupBy('network_id')
            ->get();

        $querySelect = "count(id) as receipt_total, sum(bill_amount) as bill_total, network_id";
        $receiptMonitorData['cod'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', 3)
            ->whereNull('deleted_at')
            ->groupBy('network_id')
            ->get();

        $querySelect = "count(id) as receipt_total, sum(bill_amount) as bill_total, network_id";
        $receiptMonitorData['credit'] = DB::table('receipts')
            ->select(DB::raw($querySelect))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', 2)
            ->whereNull('deleted_at')
            ->groupBy('network_id')
            ->get();

        return $receiptMonitorData;
    }

    public static function retainedReceiptsMonitor($ym)
    {
        $retainedReceiptsData = [];

        if (Cache::has('retained_receipts_data_'.$ym)) {
            return Cache::get('retained_receipts_data_'.$ym);
        }
        $retainedReceiptsData = Receipt::whereIn('status_code', array_keys(Status::getList('delivery')))
            ->where('updated_at', '<', Carbon::now()->subDays(2))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->with('lastProgress')
            ->orderBy('pickup_time','desc')
            ->get();

            Cache::put('retained_receipts_data_'.$ym, $retainedReceiptsData, 1);

        return $retainedReceiptsData;
    }

    public static function invoiceableReceiptsMonitor($ym)
    {
        $invoiceableReceiptsData = [];

        if (Cache::has('invoiceable_receipts_data_'.$ym)) {
            return Cache::get('invoiceable_receipts_data_'.$ym);
        }
        $invoiceableReceiptsData = Receipt::whereIn('status_code', array_keys(Status::getList('invoiceable')))
            ->where(function ($query) use ($ym) {
                if ($ym) {
                    $query->where('pickup_time', 'like', $ym.'%');
                }
            })
            ->where('payment_type_id', 2)
            ->whereNull('invoice_id')
            ->with('lastProgress')
            ->orderBy('pickup_time','desc')
            ->get();

            Cache::put('invoiceable_receipts_data_'.$ym, $invoiceableReceiptsData, 1);

        return $invoiceableReceiptsData;
    }

    public static function getYearMonths()
    {
        $yearMonthsDB = DB::table('receipts')
            ->select(DB::raw("extract(year_month from pickup_time) as ym"))
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('ym'))
            ->get();

        $yearMonths = [];

        foreach ($yearMonthsDB as $value) {
            $ym = substr($value->ym, 0, 4).'-'.substr($value->ym, -2);
            $yearMonths[$ym] = $ym;
        }

        return $yearMonths;
    }
}
