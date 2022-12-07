<?php

namespace App\Entities\Reports;

use DB;
use App\Entities\Receipts\Status;
use App\Entities\Receipts\Receipt;

/**
 * Dashboard Repository Class
 */
class DashboardRepository
{
    public function getReceiptList($request)
    {
        $listType = $request->segment(2);

        if (!in_array($listType, ['uninvoiced', 'invoiced', 'paid', 'per_network'])) {
            return [];
        }

        $yearMonth = $request->get('ym');
        $networkId = $request->get('network_id');

        $receipts = Receipt::where('pickup_time', 'like', $yearMonth.'%')
            ->where('receipts.network_id', $networkId)
            ->with(
                'creator', 'origin', 'customer', 'invoice',
                'lastProgress.creator', 'lastProgress.handler',
                'lastProgress.origin', 'lastProgress.destination',
                'destCity', 'destDistrict'
            );

        if ($request->get('payment_type_id')) {
            $receipts->where('payment_type_id', $request->get('payment_type_id'));
        }

        switch ($listType) {
            case 'invoiced':
                $receipts->whereNotNull('invoice_id')
                    ->whereHas('invoice', function ($query) {
                        $query->whereNull('verify_date');
                    });
                break;

            case 'paid':
                $receipts->whereNotNull('invoice_id')
                    ->whereHas('invoice', function ($query) {
                        $query->whereNotNull('verify_date');
                    });
                break;

            case 'uninvoiced':
                $receipts->whereNull('invoice_id');
                break;

            default: // per_network
                break;
        }

        $receipts = $receipts->get();

        return $receipts;
    }

    public function getReceiptSummary($yearMonth, $networkId = null, $paymentTypeId = null)
    {
        $rawSelect = "";
        $rawSelect .= "DATE(pickup_time) as date";
        $rawSelect .= ", COUNT(id) as receipt_total";
        $rawSelect .= ", CASE";
        foreach (Status::all() as $statusCode => $statusName) {
            if ($statusCode == 'ir') {
                $rawSelect .= " WHEN (status_code = 'ir' and invoice_id is null) THEN 'ir'";
                $rawSelect .= " WHEN (status_code = 'ir' and invoice_id is not null) THEN 'id'";
            } else {
                $rawSelect .= " WHEN status_code = '{$statusCode}' THEN '{$statusCode}'";
            }
        }
        $rawSelect .= " END as the_status_code";

        $receiptSummary = DB::table('receipts')
            ->select(DB::raw($rawSelect))
            ->where('pickup_time', 'like', $yearMonth.'%')
            ->whereNull('deleted_at')
            ->groupBy('date')
            ->groupBy('the_status_code');

        if ($networkId) {
            $receiptSummary->where('network_id', $networkId);
        }

        if ($paymentTypeId) {
            $receiptSummary->where('payment_type_id', $paymentTypeId);
        }
        // echo '<pre>$receiptSummary->toSql() : ', print_r($receiptSummary->toSql(), true), '</pre>';
        // die();
        // dd($receiptSummary->toSql());

        return $receiptSummary->get();
    }
}
