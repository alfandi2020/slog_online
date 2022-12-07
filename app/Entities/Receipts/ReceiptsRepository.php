<?php

namespace App\Entities\Receipts;

use App\Entities\BaseRepository;

/**
 * Receipts Repository Class
 */
class ReceiptsRepository extends BaseRepository
{
    protected $model;
    protected $_paginate = 25;

    public function __construct(Receipt $model)
    {
        parent::__construct($model);
    }

    public function getLatestReceipts($date)
    {
        if (is_null($date) || $date == '') {
            $date = date('Y-m-d');
        }

        return $this->model->orderBy('pickup_time', 'desc')
            ->orderBy('number')
            //Cari tau query apa yg mau di bikin disini??? PR penting
            ->where('network_id', auth()->user()->network_id)
            ->where('pickup_time', 'like', $date.'%')
            ->with('creator', 'origin', 'destination')
            ->paginate($this->_paginate);
    }

    public function getReceiptsByNumber($query)
    {
        if (is_null($query)) {
            return [];
        }

        return $this->model->orderBy('pickup_time', 'desc')
            ->orderBy('number')
            ->where('number', 'like', '%'.$query.'%')
            ->orWhere('reference_no', 'like', '%'.$query.'%')
            ->with('creator', 'origin', 'destination')
            ->paginate($this->_paginate);

        // return $this->model->orderBy('pickup_time', 'desc')
        //     ->orderBy('number')
        //     ->whereIn('number', explode(', ', $query))
        //     ->orWhereIn('reference_no', explode(', ', $query))
        //     ->with('creator', 'origin', 'destination')
        //     ->paginate($this->_paginate);
    }

    public function getRetailServicesList()
    {
        return ['' => '-- Pilih Layanan --'] + Service::whereCategory('retail')->lists('name', 'abb')->toArray();
    }

    public function getSalServicesList()
    {
        return ['' => '-- Pilih Layanan --'] + Service::whereCategory('sal')->lists('name', 'abb')->toArray();
    }

    public function getIntlServicesList()
    {
        return ['' => '-- Pilih Layanan --'] + Service::whereCategory('intl')->lists('name', 'abb')->toArray();
    }

    public function getReceiptDuplicates($pages = null)
    {
        $duplicates = [
            1 => trans('receipt.consignor'),
            2 => trans('receipt.consignor_network'),
            3 => trans('receipt.accounting'),
            4 => trans('receipt.consignee_network'),
            5 => trans('receipt.consignee'),
        ];

        if (is_null($pages)) {
            return $duplicates;
        }

        return array_slice($duplicates, 0, $pages, true);
    }

    protected function getNewReceiptNumber($receiptNumber = '')
    {
        if ($receiptNumber != '') {
            return $receiptNumber;
        }

        $companyId = '7';
        $networkId = auth()->user()->network->code;
        $date = date('ymd');

        $lastAgentReceipt = $this->model->where('number', 'like', $companyId.$networkId.$date.'%')
            ->orderBy('number', 'desc')
            ->first();

        if (is_null($lastAgentReceipt)) {
            return $companyId.$networkId.$date.'0001';
        } else {
            $lastAgentReceiptNumber = $lastAgentReceipt->number;
            return ++$lastAgentReceiptNumber;
        }

    }
}
