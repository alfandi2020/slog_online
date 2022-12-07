<?php

namespace App\Entities\Invoices;

use App\Entities\Receipts\Receipt;
use App\Entities\Transactions\Transaction;
use Illuminate\Database\Eloquent\Builder;
use DB;

class Cod extends Invoice
{
    protected $table = 'invoices';
    protected $casts = [
        'charge_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('cod_type', function (Builder $builder) {
            $builder->where('type_id', 3);
        });
    }

    public function setTypeIdAttribute($value)
    {
        $this->attributes['type_id'] = 3;
    }

    public function receipts()
    {
        return $this->hasMany('App\Entities\Receipts\Receipt', 'invoice_id')->orderBy('pickup_time', 'desc');
    }

    public function assignReceipt($receipts)
    {
        if ($receipts instanceof Receipt) {

            if ($receipts->payment_type_id != '3') return false;

            if ( ! $receipts->isDelivered()) return false;

            DB::beginTransaction();
            $this->receipts()->save($receipts);
            $this->amount = $this->receipts->sum('amount');
            $this->save();
            DB::commit();

            return $receipts;
        } else {
            DB::beginTransaction();
            $this->receipts()->saveMany($receipts);
            $this->amount = $receipts->sum('amount');
            $this->save();
            DB::commit();

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

        event(new \App\Events\Invoices\CodInvoiceReceived($this));
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }

    public function send()
    {
        if ($this->receipts()->count()) {
            $this->forceFill(['sent_date' => date('Y-m-d')])->save();
            event(new \App\Events\Invoices\CodInvoiceSent($this));

            return true;
        }
        return false;
    }
}
