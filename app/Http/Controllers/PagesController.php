<?php

namespace App\Http\Controllers;

use App\Entities\Networks\Network;
use App\Entities\Receipts\PaymentType;
use App\Entities\Receipts\Status;
use App\Entities\Reports\AdminDashboardQuery;
use App\Entities\Users\User;
use App\Services\ChargeCalculator;
use App\Services\CostsCalculator;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin() | auth()->user()->isBranchHead()) {
            $queriedYearMonths = request('ym', date('Y-m'));
            $users = User::select(['id', 'name', 'role_id', 'network_id'])->with('network')->get();
            $networks = Network::select(['id', 'name'])->get();
            $yearMonths = AdminDashboardQuery::getYearMonths();
            $deliveryStatuses = Status::getList('delivery');
            $invoiceableStatuses = Status::getList('invoiceable');
            $paymentTypes = PaymentType::all();

            $receiptMonitorData = null;
            if (request('tab') == 'receipt-monitor' || request('tab') == null) {
                $receiptMonitorData = AdminDashboardQuery::receiptMonitor($queriedYearMonths);
            }
            $receiptPaymentMonitorData = null;
            if ($paymentTypes->contains(request('tab'))) {
                $receiptPaymentMonitorData = AdminDashboardQuery::receiptPaymentMonitor($queriedYearMonths, $paymentTypes->search(request('tab')));
            }
            $retainedReceiptsData = null;
            if (request('tab') == 'receipt-retained') {
                $retainedReceiptsData = AdminDashboardQuery::retainedReceiptsMonitor($queriedYearMonths);
            }
            $invoiceableReceiptsData = null;
            if (request('tab') == 'receipt-uninvoiced-pod') {
                $invoiceableReceiptsData = AdminDashboardQuery::invoiceableReceiptsMonitor($queriedYearMonths);
            }

            $usersStatus = User::select("*")
                ->whereNotNull('last_seen')
                ->orderBy('last_seen', 'DESC')
                ->paginate(25);

            return view('pages.dashboard-admin', compact(
                'receiptPaymentMonitorData',
                'paymentTypes',
                'receiptMonitorData',
                'networks',
                'users',
                'deliveryStatuses',
                'retainedReceiptsData',
                'yearMonths',
                'invoiceableStatuses',
                'invoiceableReceiptsData',
                'queriedYearMonths',
                'usersStatus'
            ));
        }

        return view('pages.home');
    }

    public function getCosts(Request $request)
    {
        $costs = null;

        if ($request->has('get-costs')) {
            $this->validate($request, [
                'orig_city_id' => 'required|numeric',
                'dest_city_id' => 'required|numeric',
                'orig_district_id' => 'nullable|numeric',
                'dest_district_id' => 'nullable|numeric',
                'charged_weight' => 'required|numeric',
            ]);

            $costs = (new CostsCalculator)->calculate(
                $request->get('orig_city_id'),
                $request->get('dest_city_id'),
                $request->get('charged_weight')
            );
        }

        return view('pages.get-costs', compact('costs'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(50);
        return view('notifications.notifications', compact('notifications'));
    }
}
