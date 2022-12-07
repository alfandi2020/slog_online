<?php

namespace App\Http\Controllers\Reports;

use DB;
use App\Http\Controllers\Controller;

class ManifestController extends Controller
{
    public function distributions()
    {
        $yearMonth = request('ym', date('Y-m'));
        $yearMonthList = $this->getYearMonthList();
        $manifests = $this->getManifestList($yearMonth, 'distribution');

        return view('reports.manifests.distributions', compact(
            'yearMonthList',
            'yearMonth',
            'manifests'
        ));
    }

    private function getManifestList($yearMonth, $type = 'distribution')
    {
        $rawQuery = "`manifests`.`id`, `manifests`.`number`, date(`manifests`.`created_at`) as `date`";
        $rawQuery .= ",(
            select count(*) from `receipts`
            inner join `receipt_progress` on `receipts`.`id` = `receipt_progress`.`receipt_id`
            where `manifests`.`id` = `receipt_progress`.`manifest_id` and `receipts`.`deleted_at` is null
        ) as `receipts_count`";
        $rawQuery .= ",(
            select sum(`weight`) from `receipts`
            inner join `receipt_progress` on `receipts`.`id` = `receipt_progress`.`receipt_id`
            where `manifests`.`id` = `receipt_progress`.`manifest_id` and `receipts`.`deleted_at` is null
        ) as `receipts_weight`";
        $rawQuery .= ",(
            select sum(`pcs_count`) from `receipts`
            inner join `receipt_progress` on `receipts`.`id` = `receipt_progress`.`receipt_id`
            where `manifests`.`id` = `receipt_progress`.`manifest_id` and `receipts`.`deleted_at` is null
        ) as `receipts_pcs`";
        $rawQuery .= ",(
            select sum(`items_count`) from `receipts`
            inner join `receipt_progress` on `receipts`.`id` = `receipt_progress`.`receipt_id`
            where `manifests`.`id` = `receipt_progress`.`manifest_id` and `receipts`.`deleted_at` is null
        ) as `receipts_items`";
        $rawQuery .= ",(
            select sum(`bill_amount`) from `receipts`
            inner join `receipt_progress` on `receipts`.`id` = `receipt_progress`.`receipt_id`
            where `manifests`.`id` = `receipt_progress`.`manifest_id` and `receipts`.`deleted_at` is null
        ) as `receipts_bill_amount`";
        $rawQuery .= ", `users`.`name` as `courier`";
        $rawQuery .= ", `cities`.`name` as `destination`";

        $manifestQuery = DB::table('manifests')
            ->select(DB::raw($rawQuery))
            ->leftJoin('users', 'users.id', 'manifests.handler_id')
            ->leftJoin('cities', 'cities.id', 'manifests.dest_city_id')
            ->where('manifests.created_at', 'like', $yearMonth.'%')
            ->where('type_id', 3)
            ->orderBy('manifests.created_at', 'desc');

        return $manifestQuery->get();
    }

    private function getYearMonthList()
    {
        $yearMonthsDB = DB::table('manifests')
            ->select(DB::raw("extract(year_month from created_at) as ym"))
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
