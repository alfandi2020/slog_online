<?php

namespace App\Entities\Networks;

use App\Entities\BaseRepository;
use DB;
use Illuminate\Database\Eloquent\Model;

class NetworksRepository extends BaseRepository
{
    protected $model;

    public function __construct(Network $model)
    {
        parent::__construct($model);
    }

    public function getNetworkYearlyReports($year, $paymentType = '', $networkId = null)
    {
        $reportsData = DB::table('receipts')->select(DB::raw("MONTH(pickup_time) as month, count(`id`) as count, sum(`bill_amount`) as total"))
            ->where(DB::raw('YEAR(pickup_time)'), $year)
            ->where(function($query) use ($paymentType, $networkId) {
                if ($paymentType != '')
                    $query->where('payment_type_id', $paymentType);
                if (!is_null($networkId))
                    $query->where('network_id', $networkId);
            })
            ->groupBy(DB::raw('YEAR(pickup_time)'))
            ->groupBy(DB::raw('MONTH(pickup_time)'))
            ->orderBy('month','asc')
            ->get();

        $reports = [];

        // foreach ($reportsData as $report) {
        //     $key = str_pad($report->month, 2, '0', STR_PAD_LEFT);
        //     $reports[$key] = $report;
        // }

        foreach (\getMonths() as $monthNumber => $monthName) {
            $comoditiesArray = [];
            $report = $reportsData->filter(function($report) use ($monthNumber) {
                return $report->month == (int) $monthNumber;
            })->first();
            $total = $report ? $report->total : 0;
            $reports[] = ['month' => $monthNumber, 'value' => $total];
        }

        return $reports;
    }


}