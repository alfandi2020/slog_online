<?php

namespace App\Entities\Reports;

use DB;
use Carbon\Carbon;
use App\Entities\Users\User;
use App\Entities\Invoices\Invoice;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Customers\Customer;
use App\Entities\References\Reference;

/**
 * Reports Repository Class
 */
class ReportsRepository
{
    public function getDailyReports($startDate, $endDate, $paymentType, $customerId)
    {
        $startDate = $startDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';

        return Receipt::whereBetween('pickup_time', [$startDate, $endDate])
            ->where(function ($query) use ($paymentType, $customerId) {
                if ($paymentType != '') {
                    $query->where('payment_type_id', $paymentType);
                }

                if (!is_null($customerId) && $customerId == 0) {
                    $query->whereNull('customer_id');
                }

                if (!is_null($customerId) && $customerId > 0) {
                    $query->where('customer_id', $customerId);
                }
            })
            ->with(['creator', 'origCity', 'origDistrict', 'destCity', 'destDistrict', 'packType'])->get();
    }

    public function getMonthlyReports($year, $month, $paymentType = '', $customerId = null)
    {
        $reportsData = DB::table('receipts')->select(DB::raw("DATE(pickup_time) as date, count(`id`) as count, sum(`bill_amount`) as total"))
            ->where(DB::raw('YEAR(pickup_time)'), $year)
            ->where(DB::raw('MONTH(pickup_time)'), $month)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($paymentType, $customerId) {
                if ($paymentType != '') {
                    $query->where('payment_type_id', $paymentType);
                }

                if (!is_null($customerId) && $customerId == 0) {
                    $query->whereNull('customer_id');
                }

                if (!is_null($customerId) && $customerId > 0) {
                    $query->where('customer_id', $customerId);
                }
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $reports = [];
        foreach ($reportsData as $report) {
            $key = substr($report->date, -2);
            $reports[$key] = $report;
        }

        return $reports;
    }

    public function getYearlyReports($year, $paymentType = '', $customerId = null)
    {
        $reportsData = DB::table('receipts')->select(DB::raw("MONTH(pickup_time) as month, count(`id`) as count, sum(`bill_amount`) as total"))
            ->where(DB::raw('YEAR(pickup_time)'), $year)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($paymentType, $customerId) {
                if ($paymentType != '') {
                    $query->where('payment_type_id', $paymentType);
                }

                if (!is_null($customerId) && $customerId == 0) {
                    $query->whereNull('customer_id');
                }

                if (!is_null($customerId) && $customerId > 0) {
                    $query->where('customer_id', $customerId);
                }
            })
            ->groupBy(DB::raw('YEAR(pickup_time)'))
            ->groupBy(DB::raw('MONTH(pickup_time)'))
            ->orderBy('month', 'asc')
            ->get();

        $reports = [];
        foreach ($reportsData as $report) {
            $key = str_pad($report->month, 2, '0', STR_PAD_LEFT);
            $reports[$key] = $report;
        }

        return $reports;
    }

    public function getUnpaidInvoices($type)
    {
        $invoices = Invoice::whereType($type)->wherePaymentDate(null)->get();
        return $invoices;
    }

    public function getReceiptsList($receiptQuery)
    {
        $startDate = $receiptQuery['start_date'] . ' 00:00:00';
        $endDate = $receiptQuery['end_date'] . ' 23:59:59';

        $receiptDbQuery = Receipt::whereBetween('pickup_time', [$startDate, $endDate])
            ->where(function ($query) use ($receiptQuery) {

                $customerId = $receiptQuery['customer_id'];

                if (!is_null($customerId) && $customerId == 0) {
                    $query->whereNull('customer_id');
                }

                if (!is_null($customerId) && $customerId > 0) {
                    $query->where('customer_id', $customerId);
                }

                if ($receiptQuery['payment_type_id']) {
                    $query->where('payment_type_id', $receiptQuery['payment_type_id']);
                }

                if ($receiptQuery['status_code']) {
                    if ($receiptQuery['status_code'] == 'ir') {
                        $query->where('status_code', $receiptQuery['status_code']);
                        $query->whereNull('invoice_id');
                    } else if ($receiptQuery['status_code'] == 'id') {
                        $query->where('status_code', 'ir');
                        $query->whereNotNull('invoice_id');
                    } else {
                        $query->where('status_code', $receiptQuery['status_code']);
                    }
                }

                if ($destCityId = $receiptQuery['dest_city_id']) {
                    $query->where('dest_city_id', $destCityId);

                    if ($destDistrictId = $receiptQuery['dest_district_id']) {
                        $query->where('dest_district_id', $destDistrictId);
                    }
                }

                $networkId = $receiptQuery['network_id'];

                if (!is_null($networkId) && $networkId == 0) {
                    $query->whereNull('network_id');
                }

                if (!is_null($networkId) && $networkId > 0) {
                    $query->where('network_id', $networkId);
                }
            });

        if ($receiptQuery['courier_id']) {
            $receiptDbQuery->whereHas('manifests', function ($query) use ($receiptQuery) {
                $query->where('receipt_progress.creator_id', $receiptQuery['courier_id']);
            });
        }

        $receiptDbQuery->with(
            'origCity',
            'destCity',
            'destDistrict',
            'packType',
            'deliveryCourier',
            'pod',
            'creator',
            'lastProgress.origin',
            'lastProgress.destination'
        );

        return $receiptDbQuery->get();
    }

    public function getUnreturnedReceipts($older = null)
    {
        return Receipt::select(DB::raw('receipts.*, DATE(receipts.pickup_time) as date'))
            ->where(function ($query) use ($older) {
                $query->whereNotIn('status_code', ['de', 'rt', 'ma', 'ir', 'id']);

                if ($older) {
                    $query->where('pickup_time', '<', Carbon::now()->subMonths(3));
                } else {
                    $query->where('pickup_time', '>=', Carbon::now()->subMonths(3));
                }
            })
            ->with('customer', 'destination')
            ->orderBy('pickup_time', 'desc')
            ->get()->groupBy('date');
    }

    public function getReturnedReceipts($date)
    {
        $rawSelect = "receipts.number, receipt_progress.receipt_id";
        $rawSelect .= ", time(receipt_progress.created_at) as returned_time";
        $rawSelect .= ", users.name as officer_name";

        return DB::table('receipt_progress')->select(DB::raw($rawSelect))
            ->leftJoin('receipts', 'receipt_progress.receipt_id', 'receipts.id')
            ->leftJoin('users', 'receipt_progress.handler_id', 'users.id')
            ->where('receipt_progress.end_status', 'rt')
            ->whereDate('receipt_progress.created_at', $date)
            ->get();
    }

    public function getRetainedReceipts($date, $officerId, $statusCode)
    {
        $statusCodes = is_null($statusCode) ? ['de', 'mw', 'rw', 'mn', 'ot', 'rd', 'od'] : [$statusCode];
        $receiptsList = Receipt::whereIn('status_code', $statusCodes)
            ->where('updated_at', '<', Carbon::now()->subDays(2))
            ->where('pickup_time', 'like', $date . '%')
            ->with(
                'customer',
                'creator',
                'origin',
                'destination',
                'lastProgress.origin',
                'lastProgress.destination',
                'lastProgress.creator',
                'lastProgress.handler'
            )
            ->orderBy('pickup_time', 'desc')
            ->get();

        if (!is_null($officerId)) {
            return $receiptsList->filter(function ($receipt) use ($officerId) {
                return $receipt->lastOfficerId() == $officerId;
            })
                ->values();
        }

        return $receiptsList;
    }

    public function getUnSentReceipts()
    {
        return Receipt::whereNotIn('status_code', ['dl', 'bd', 'or', 'rt', 'ma', 'ir'])
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->with(
                'customer',
                'creator',
                'origin',
                'destination',
                'lastProgress.origin',
                'lastProgress.destination',
                'lastProgress.creator',
                'lastProgress.handler'
            )
            ->orderBy('pickup_time', 'desc')
            ->get();
    }

    public function getComodityMonthlyReports($comodities, $year, $month, $paymentType = '', $comodityId = null)
    {
        return $this->getFormattedComodityMonthlyReports(
            $this->filterComodities($comodities, $comodityId),
            $this->getRawComodityMonthlyReports($year, $month, $comodityId),
            $year,
            $month
        );
    }

    private function getFormattedComodityMonthlyReports($comodities, $reportsData, $year, $month)
    {
        // $reports = [];
        // foreach ($reportsData as $report) {
        //     $comoditiesArray = [];
        //     foreach ($comodities as $id => $comodity) {
        //         $comoditiesArray[$comodity] = $report->comodity_name == $comodity ? $report->total : 0;
        //         if (isset($reports[$report->date]) && $reports[$report->date][$comodity] == 0 && $comoditiesArray[$comodity] != 0)
        //             $reports[$report->date][$comodity] = $report->total;
        //     }
        //     if (! isset($reports[$report->date]))
        //         $reports[$report->date] = ['date' => $report->date] + $comoditiesArray;
        // }

        // return array_values($reports);
        //
        $reports = [];
        foreach (\monthDateArray($year, $month) as $dateNumber) {
            $comoditiesArray = [];
            foreach ($comodities as $id => $comodity) {
                $report = $reportsData->filter(function ($report) use ($comodity, $dateNumber, $year, $month) {
                    return $report->comodity_name == $comodity && $report->date == $year . '-' . $month . '-' . $dateNumber;
                })->first();
                $comoditiesArray[$comodity] = $report ? $report->total : 0;
            }
            $reports[] = ['date' => $dateNumber] + $comoditiesArray;
        }

        return $reports;
    }

    private function getRawComodityMonthlyReports($year, $month, $comodityId = null)
    {
        $rawSelect = "DATE(`receipts`.`pickup_time`) as date, ";
        $rawSelect .= "ifnull(`site_references`.`name`, 'Umum') as comodity_name, ";
        $rawSelect .= "count(`receipts`.`id`) as count, ";
        $rawSelect .= "`customers`.`comodity_id`, ";
        $rawSelect .= "sum(`receipts`.`bill_amount`) as total";

        return DB::table('receipts')->select(DB::raw($rawSelect))
            ->leftJoin('customers', 'customers.id', '=', 'receipts.customer_id')
            ->leftJoin('site_references', 'site_references.id', '=', 'customers.comodity_id')
            ->groupBy('site_references.name')
            ->groupBy('customers.comodity_id')
            ->groupBy('date')
            ->where(DB::raw('YEAR(`receipts`.`pickup_time`)'), $year)
            ->where(DB::raw('MONTH(`receipts`.`pickup_time`)'), $month)
            ->where(function ($query) use ($comodityId) {
                if ($comodityId == 1) {
                    $query->whereNull('receipts.customer_id');
                } else if ($comodityId > 1) {
                    $query->where('customers.comodity_id', $comodityId);
                }
            })
            ->get();
    }

    public function getComodityYearlyReports($comodities, $year, $paymentType = '', $comodityId = null)
    {
        return $this->getFormattedComodityYearlyReports(
            $this->filterComodities($comodities, $comodityId),
            $this->getRawComodityYearlyReports($year, $comodityId)
        );
    }

    private function getFormattedComodityYearlyReports($comodities, $reportsData)
    {
        $reports = [];
        foreach (\getMonths() as $monthNumber => $monthName) {
            $comoditiesArray = [];
            foreach ($comodities as $id => $comodity) {
                $report = $reportsData->filter(function ($report) use ($comodity, $monthNumber) {
                    return $report->comodity_name == $comodity && $report->month == (int) $monthNumber;
                })->first();
                $comoditiesArray[$comodity] = $report ? $report->total : 0;
            }
            $reports[] = ['month' => $monthName] + $comoditiesArray;
        }

        return $reports;
    }

    private function filterComodities($comodities, $comodityId = null)
    {
        return is_null($comodityId) ? $comodities
            : array_filter($comodities, function ($key) use ($comodityId) {
                return $comodityId == $key;
            }, ARRAY_FILTER_USE_KEY);
    }

    private function getRawComodityYearlyReports($year, $comodityId = null)
    {
        $rawSelect = "month(`receipts`.`pickup_time`) as month, ";
        $rawSelect .= "ifnull(`site_references`.`name`, 'Umum') as comodity_name, ";
        $rawSelect .= "count(`receipts`.`id`) as count, ";
        $rawSelect .= "`customers`.`comodity_id`, ";
        $rawSelect .= "sum(`receipts`.`bill_amount`) as total";

        return DB::table('receipts')->select(DB::raw($rawSelect))
            ->leftJoin('customers', 'customers.id', '=', 'receipts.customer_id')
            ->leftJoin('site_references', 'site_references.id', '=', 'customers.comodity_id')
            ->groupBy('site_references.name')
            ->groupBy('customers.comodity_id')
            ->groupBy(DB::raw('YEAR(pickup_time)'))
            ->groupBy(DB::raw('MONTH(pickup_time)'))
            ->where(DB::raw('YEAR(`receipts`.`pickup_time`)'), $year)
            ->where(function ($query) use ($comodityId) {
                if ($comodityId == 1) {
                    $query->whereNull('receipts.customer_id');
                } else if ($comodityId > 1) {
                    $query->where('customers.comodity_id', $comodityId);
                }
            })
            ->get();
    }

    public function getCustomersList()
    {
        return ['Customer Umum'] + Customer::orderBy('name')->pluck('name', 'id')->all();
    }

    public function getAllCourierList()
    {
        return User::where('role_id', 7)
            ->where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getComoditiesList()
    {
        return Reference::whereCat('comodity')->pluck('name', 'id')->all();
    }

    public function getNetworksList()
    {
        return Network::pluck('name', 'id')->all();
    }

    public function getNetworkDailyReports($startDate, $endDate, $paymentType, $networkId)
    {
        $startDate = $startDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';

        return Receipt::whereBetween('pickup_time', [$startDate, $endDate])
            ->where(function ($query) use ($paymentType, $networkId) {
                if ($paymentType != '') {
                    $query->where('payment_type_id', $paymentType);
                }

                if (!is_null($networkId) && $networkId > 0) {
                    $query->where('network_id', $networkId);
                }
            })
            ->with(['creator', 'origCity', 'origDistrict', 'destCity', 'destDistrict', 'packType'])->get();
    }

    public function getNetworkMonthlyReports($year, $month, $paymentType = '', $networkId = null)
    {
        $reportsData = DB::table('receipts')
            ->select(DB::raw("DATE(pickup_time) as date, count(`id`) as count, sum(`bill_amount`) as total"))
            ->where(DB::raw('YEAR(pickup_time)'), $year)
            ->where(DB::raw('MONTH(pickup_time)'), $month)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($paymentType, $networkId) {
                if ($paymentType != '') {
                    $query->where('payment_type_id', $paymentType);
                }

                if (!is_null($networkId) && $networkId == 0) {
                    $query->whereNull('network_id');
                }

                if (!is_null($networkId) && $networkId > 0) {
                    $query->where('network_id', $networkId);
                }
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $reports = [];
        foreach ($reportsData as $report) {
            $key = substr($report->date, -2);
            $reports[$key] = $report;
        }

        return $reports;
    }

    public function getNetworkYearlyReports($year, $paymentType = '', $networkId = null)
    {
        $reportsData = DB::table('receipts')
            ->select(DB::raw("MONTH(pickup_time) as month, count(`id`) as count, sum(`bill_amount`) as total"))
            ->where(DB::raw('YEAR(pickup_time)'), $year)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($paymentType, $networkId) {
                if ($paymentType != '') {
                    $query->where('payment_type_id', $paymentType);
                }

                if (!is_null($networkId)) {
                    $query->where('network_id', $networkId);
                }
            })
            ->groupBy(DB::raw('YEAR(pickup_time)'))
            ->groupBy(DB::raw('MONTH(pickup_time)'))
            ->orderBy('month', 'asc')
            ->get();

        $reports = [];

        foreach ($reportsData as $report) {
            $key = str_pad($report->month, 2, '0', STR_PAD_LEFT);
            $reports[$key] = $report;
        }

        return $reports;
    }
}
