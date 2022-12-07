<?php

namespace App\Http\Controllers\Reports;

use App\Entities\Customers\Customer;
use App\Entities\Networks\Network;
use App\Entities\Reports\ReportsRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NetworkOmzetController extends Controller
{
	private $repo;

	public function __construct(ReportsRepository $repo)
	{
	    $this->repo = $repo;
	}

	public function daily(Request $request)
	{
		$customer  = null;
		$startDate = $request->get('start_date', date('Y-m-d'));
		$endDate   = $request->get('end_date', date('Y-m-d'));

		// Invalid Date Check
        if (! isValidDate($startDate) || ! isValidDate($endDate))
            return redirect()->route('reports.network-omzet.monthly');

		if ($request->has('network_id'))
			$network = Customer::find($request->get('network_id'));

		$networks = $this->repo->getNetworksList();

		$receipts = $this->repo->getNetworkDailyReports($startDate, $endDate, $request->get('payment'), $request->get('network_id'));

		return view('reports.network-omzet.daily', compact('receipts', 'startDate', 'endDate', 'network', 'networks'));
	}

	public function monthly(Request $request)
	{
		$network = null;
		$year     = $request->get('year', date('Y'));
		$month    = $request->get('month', date('m'));

		// Invalid Date Check
        if (! isValidDate($year . '-' . $month . '-01'))
            return redirect()->route('reports.network-omzet.monthly');

		if ($request->has('network_id'))
			$network = Network::find($request->get('network_id'));

		$networks = $this->repo->getNetworksList();

		$reports = $this->repo->getNetworkMonthlyReports($year, $month, $request->get('payment'), $request->get('network_id'));

		return view('reports.network-omzet.monthly', compact('reports', 'month', 'year', 'network', 'networks'));
	}

	public function yearly(Request $request)
	{
		$network = null;
		$year = $request->get('year', date('Y'));

		if ($request->has('network_id'))
			$network = Network::find($request->get('network_id'));

		$networks = $this->repo->getNetworksList();

		$reports = $this->repo->getNetworkYearlyReports($year, $request->get('payment'), $request->get('network_id'));

		return view('reports.network-omzet.yearly', compact('reports','year','network', 'networks'));
	}
}
