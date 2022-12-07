<?php

namespace App\Entities\Receipts;

/**
* Receipt Query Object
*/
class ReceiptQuery
{
    public function getNetworkLatest()
    {
        return Receipt::select(['number','customer_id','created_at'])
            ->where('network_id', auth()->user()->network_id)
            ->latest()
            ->limit(10)
            ->get();
    }
}