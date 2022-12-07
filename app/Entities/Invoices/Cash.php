<?php

namespace App\Entities\Invoices;

use App\Entities\Receipts\Receipt;
use App\Entities\Transactions\Transaction;
use Illuminate\Database\Eloquent\Builder;

class Cash extends Invoice
{
    protected $table = 'invoices';
    protected $casts = [
        'charge_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('cash_type', function (Builder $builder) {
            $builder->where('type_id', 1);
        });
    }

    public function setTypeIdAttribute($value)
    {
        $this->attributes['type_id'] = 1;
    }

    public function receipts()
    {
        return $this->hasMany('App\Entities\Receipts\Receipt', 'invoice_id')->orderBy('pickup_time', 'desc');
    }

    public function assignReceipt($receipts)
    {
        if ($receipts instanceof Receipt) {

            if ($receipts->payment_type_id != '1') return false;

            $this->receipts()->save($receipts);
            return $receipts;
        } else {
            $this->receipts()->saveMany($receipts);
            return $receipts->count();
        }
    }

    public function verify()
    {
        $today = date('Y-m-d');
        $this->forceFill([
            'payment_date' => $today,
            'verify_date' => $today,
            'handler_id' => auth()->id(),
        ])->save();

        event(new \App\Events\Invoices\CashInvoiceReceived($this));
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }

    public function send()
    {
        if ($this->receipts()->count()) {
            $this->forceFill(['sent_date' => date('Y-m-d')])->save();
            event(new \App\Events\Invoices\CashInvoiceSent($this));
        }

        return false;
    }
}
