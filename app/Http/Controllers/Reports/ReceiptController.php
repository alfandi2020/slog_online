<?php

namespace App\Http\Controllers\Reports;

use Excel;
use Carbon\Carbon;
use App\Entities\Users\User;
use Illuminate\Http\Request;
use App\Entities\Regions\Province;
use App\Http\Controllers\Controller;
use App\Entities\Reports\ReportsRepository;

class ReceiptController extends Controller
{
    private $repo;

    public function __construct(ReportsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function export(Request $request)
    {
        $receiptQuery['start_date'] = $request->get('start_date', (new Carbon)->subdays(2)->format('Y-m-d'));
        $receiptQuery['end_date'] = $request->get('end_date', date('Y-m-d'));
        $receiptQuery['payment_type_id'] = $request->get('payment_type_id');
        $receiptQuery['customer_id'] = $request->get('customer_id');
        $receiptQuery['courier_id'] = $request->get('courier_id');
        $receiptQuery['status_code'] = $request->get('status_code');
        $receiptQuery['dest_city_id'] = $request->get('dest_city_id');
        $receiptQuery['dest_district_id'] = $request->get('dest_district_id');
        $receiptQuery['network_id'] = $request->get('network_id');

        $receipts = $this->repo->getReceiptsList($receiptQuery);

        if ($request->get('export') == 1) {
            // return view('reports.receipts.export-xls', compact('receipts', 'receiptQuery'));
            Excel::create('namafile', function ($excel) use ($receipts, $receiptQuery) {
                $excel->sheet('Export', function ($sheet) use ($receipts, $receiptQuery) {
                    $sheet->loadView('reports.receipts.export-xls', compact('receipts', 'receiptQuery'));
                });
            })->export('xls');
        }

        $customers = $this->repo->getCustomersList();
        $couriers = $this->repo->getAllCourierList();
        $networks = $this->repo->getNetworksList();
        $cities = $this->getCities();

        return view('reports.receipts.export', compact(
            'receipts',
            'receiptQuery',
            'customers',
            'cities',
            'couriers',
            'networks'
        ));
    }

    public function unreturned(Request $request)
    {
        $datedReceipts = $this->repo->getUnreturnedReceipts($request->get('older'));
        return view('reports.receipts.unreturned', compact('datedReceipts'));
    }

    public function returned(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $receipts = $this->repo->getReturnedReceipts($date);

        return view('reports.receipts.returned', compact('date', 'receipts'));
    }

    public function retained(Request $request)
    {
        $receipts = $this->repo->getRetainedReceipts($request->get('date'), $request->get('officer_id'), $request->get('status_code'));
        $users = User::orderBy('name')->pluck('name', 'id');
        return view('reports.receipts.retained', compact('receipts', 'users'));
    }

    public function late(Request $request)
    {
        $receipts = $this->repo->getUnSentReceipts();
        return view('reports.receipts.late', compact('receipts'));
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
}
