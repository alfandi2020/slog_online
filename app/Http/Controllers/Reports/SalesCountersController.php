<?php

namespace App\Http\Controllers\Reports;

use App\Entities\Reports\ReportsRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;

class SalesCountersController extends Controller
{
    private $repo;

    public function __construct(ReportsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function daily(Request $request)
    {
        $receiptQuery['start_date'] = $request->get('start_date', date('Y-m-d'));
        $receiptQuery['end_date'] = $request->get('end_date', date('Y-m-d'));
        $receiptQuery['payment_type_id'] = $request->get('payment');
        $receiptQuery['status_code'] = null;
        $receiptQuery['customer_id'] = $request->get('customer_id');
        $receiptQuery['dest_city_id'] = $request->get('dest_city_id');
        $receiptQuery['dest_district_id'] = $request->get('dest_district_id');

        // Invalid Date Check
        if (! isValidDate($receiptQuery['start_date']) || ! isValidDate($receiptQuery['end_date']))
            return redirect()->route('reports.sales-counter.daily');

        $receipts = $this->repo->getDailyReports(
            $receiptQuery['start_date'], $receiptQuery['end_date'],
            $receiptQuery['payment_type_id'], null
        );

        if ($request->get('export') == 1) {
            $documentTitle = 'Laporan Harian Sales Counter';

            // return view('reports.receipts.export-xls', compact('receipts', 'receiptQuery', 'documentTitle'));

            $filename = 'report-'.$receiptQuery['start_date'].'-'.$receiptQuery['end_date'];

            Excel::create($filename, function($excel) use ($receipts, $receiptQuery, $documentTitle) {
                $excel->sheet('Export', function($sheet) use ($receipts, $receiptQuery, $documentTitle) {
                    $sheet->loadView('reports.receipts.export-xls', compact('receipts', 'receiptQuery', 'documentTitle'));
                });
            })->export('xls');
        }

        return view('reports.sales-counter.daily', compact('receipts', 'receiptQuery'));
    }
}
