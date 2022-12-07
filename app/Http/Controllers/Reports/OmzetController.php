<?php

namespace App\Http\Controllers\Reports;

use App\Entities\Customers\Customer;
use App\Entities\Reports\ReportsRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OmzetController extends Controller
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
            return redirect()->route('reports.omzet.daily');

		if ($request->has('customer_id'))
			$customer = Customer::find($request->get('customer_id'));

		$customers = $this->repo->getCustomersList();

		$receipts    = $this->repo->getDailyReports($startDate, $endDate, $request->get('payment'), $request->get('customer_id'));

		return view('reports.daily', compact('receipts', 'startDate', 'endDate', 'customer', 'customers'));
	}

	public function monthly(Request $request)
	{
		$customer = null;
		$year     = $request->get('year', date('Y'));
		$month    = $request->get('month', date('m'));

		// Invalid Date Check
        if (! isValidDate($year . '-' . $month . '-01'))
            return redirect()->route('reports.omzet.monthly');

		if ($request->has('customer_id'))
			$customer = Customer::find($request->get('customer_id'));

		$customers = $this->repo->getCustomersList();

		$reports = $this->repo->getMonthlyReports($year, $month, $request->get('payment'), $request->get('customer_id'));

		return view('reports.monthly', compact('reports', 'month', 'year', 'customer', 'customers'));
	}

	public function yearly(Request $request)
	{
		$customer = null;
		$year     = $request->get('year', date('Y'));

		if ($request->has('customer_id'))
			$customer = Customer::find($request->get('customer_id'));

		$customers = $this->repo->getCustomersList();

		$reports = $this->repo->getYearlyReports($year, $request->get('payment'), $request->get('customer_id'));

		return view('reports.yearly', compact('reports','year','customer', 'customers'));
	}

	public function unpaidCustomer()
	{
		$invoices = $this->repo->getUnpaidInvoices('customer');
		return view('reports.unpaid.customer', compact('invoices'));
	}

}
