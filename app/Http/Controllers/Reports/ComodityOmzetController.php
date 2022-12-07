<?php

namespace App\Http\Controllers\Reports;

use App\Entities\Customers\Customer;
use App\Entities\References\Reference;
use App\Entities\Reports\ReportsRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComodityOmzetController extends Controller
{
	private $repo;

	public function __construct(ReportsRepository $repo)
	{
	    $this->repo = $repo;
	}

	public function monthly(Request $request)
	{
		$comodity = null;
		$year     = $request->get('year', date('Y'));
		$month    = $request->get('month', date('m'));

		// Invalid Date Check
        if (! isValidDate($year . '-' . $month . '-01'))
            return redirect()->route('reports.comodity-omzet.monthly');

		if ($request->has('comodity_id'))
			$comodity = Reference::find($request->get('comodity_id'));

		$comodities = [1 => 'Umum'] + $this->repo->getComoditiesList();

		$reports = $this->repo->getComodityMonthlyReports($comodities, $year, $month, $request->get('payment'), $request->get('comodity_id'));

		return view('reports.comodity-omzet.monthly', compact('reports', 'month', 'year', 'comodity', 'comodities'));
	}

	public function yearly(Request $request)
	{
		$comodity = null;
		$year = $request->get('year', date('Y'));

		if ($request->has('comodity_id'))
			$comodity = Reference::find($request->get('comodity_id'));

		$comodities = [1 => 'Umum'] + $this->repo->getComoditiesList();

		$reports = $this->repo->getComodityYearlyReports($comodities, $year, $request->get('payment'), $request->get('comodity_id'));

		return view('reports.comodity-omzet.yearly', compact('reports','year','comodity', 'comodities'));
	}
}
