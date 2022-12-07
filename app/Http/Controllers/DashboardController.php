<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\Receipts\Status;
use App\Entities\Networks\Network;
use App\Entities\Receipts\PaymentType;
use App\Entities\Reports\DashboardRepository;

class DashboardController extends Controller
{
    private $repo;

    public function __construct(DashboardRepository $repo)
    {
        $this->repo = $repo;
    }

    public function perNetwork(Request $request)
    {
        $receipts = $this->repo->getReceiptList($request);

        return view('dashboard.monitor.receipt-list', compact('receipts'));
    }

    public function uninvoiced(Request $request)
    {
        $receipts = $this->repo->getReceiptList($request);

        return view('dashboard.monitor.receipt-list', compact('receipts'));
    }

    public function invoiced(Request $request)
    {
        $receipts = $this->repo->getReceiptList($request);

        return view('dashboard.monitor.receipt-list', compact('receipts'));
    }

    public function paid(Request $request)
    {
        $receipts = $this->repo->getReceiptList($request);

        return view('dashboard.monitor.receipt-list', compact('receipts'));
    }

    public function chart(Request $request)
    {
        $year = $this->getYear();
        $month = $this->getMonth();
        $monthDateArray = monthDateArray($year, $month);
        $yearMonth = $year.'-'.$month;

        $receiptSummary = $this->repo->getReceiptSummary(
            $yearMonth, $request->get('network_id'), $request->get('payment_type_id')
        );
        $receiptStatusList = Status::toArray();
        $paymentTypeList = PaymentType::toArray();
        $networkList = Network::pluck('name', 'id')->toArray();

        return view('dashboard.monitor.chart', compact(
            'monthDateArray', 'yearMonth', 'receiptSummary', 'receiptStatusList',
            'year', 'month', 'paymentTypeList', 'networkList'
        ));
    }

    private function getYear()
    {
        $year = request('year');

        return array_key_exists($year, getYears()) ? $year : date('Y');
    }

    private function getMonth()
    {
        $month = request('month');

        return array_key_exists($month, getMonths()) ? $month : date('m');
    }
}
