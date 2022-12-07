<?php

namespace App\Entities\Customers;

use App\Entities\BaseRepository;

class CustomersRepository extends BaseRepository
{
    protected $model;

    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function getCustomersList($q, $categoryId = null)
    {
        $rawSelect = '`customers`.*, ';
        $rawSelect .= '(select count(*) from `rates` where `customers`.`id` = `rates`.`customer_id`) as `rates_count`, ';
        $rawSelect .= '(select count(*) from `receipts` where `customers`.`id` = `receipts`.`customer_id` and `receipts`.`invoice_id` is null) as `receipts_count`, ';
        $rawSelect .= '(select sum(`receipts`.`bill_amount`) from `receipts` where `customers`.`id` = `receipts`.`customer_id` and `receipts`.`invoice_id` is null) as `bill_sum`';
        $customerQuery = Customer::latest()
            ->select(\DB::raw($rawSelect))
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%');
                $query->orWhere('account_no', 'like', '%'.$q.'%');
            })
            ->orderBy('bill_sum', 'desc')
            ->orderBy('customers.name', 'asc');

        if ($categoryId) {
            $customerQuery->where('category_id', $categoryId);
        }

        return $customerQuery
            ->with('comodity', 'network')
            ->paginate(100);
    }
}
