<?php

namespace App\Entities\Transactions;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'number', 'invoice_id', 'in_out',
        'amount', 'creator_id', 'verified_by',
        'verified_at', 'notes', 'payment_method_id',
    ];

    public function generateNumber()
    {
        $prefix = date('ym');
        $lastTransaction = $this->orderBy('number', 'desc')->first();
        if ($lastTransaction) {
            $lastTransactionNumber = $lastTransaction->number;
            if (substr($lastTransactionNumber, 0, 4) == $prefix) {
                return ++$lastTransactionNumber;
            }
        }

        return $prefix.'0001';
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
