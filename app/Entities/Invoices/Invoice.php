<?php

namespace App\Entities\Invoices;

use DB;
use App\Entities\Receipts\Receipt;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Entities\Transactions\Transaction;
use App\Exceptions\InvoiceReceiptException;

class Invoice extends Model
{
    use PresentableTrait;
    protected $presenter = InvoicePresenter::class;
    protected $casts = [
        'charge_details' => 'array',
        'delivery_info'  => 'array',
    ];

    public function receipts()
    {
        return $this->hasMany('App\Entities\Receipts\Receipt');
    }

    public function customer()
    {
        return $this->belongsTo('App\Entities\Customers\Customer');
    }

    public function network()
    {
        return $this->belongsTo('App\Entities\Networks\Network');
    }

    public function creator()
    {
        return $this->belongsTo('App\Entities\Users\User');
    }

    public function handler()
    {
        return $this->belongsTo('App\Entities\Users\User');
    }

    public function isOnProccess()
    {
        return is_null($this->sent_date);
    }

    public function scopeIsOnProccess($query)
    {
        return $query->whereNull('sent_date');
    }

    public function isSent()
    {
        return !!$this->sent_date;
    }

    public function scopeIsSent($query)
    {
        return $query->whereNotNull('sent_date')
            ->whereNull('payment_date')
            ->whereNull('problem_date');
    }

    public function isPaid()
    {
        return !!$this->payment_date;
    }

    public function scopeIsPaid($query)
    {
        return $query->whereNotNull('payment_date')->whereNull('verify_date');
    }

    public function isVerified()
    {
        return !!$this->verify_date;
    }

    public function scopeIsVerified($query)
    {
        return $query->whereNotNull('verify_date');
    }

    public function assignReceipt($receipts)
    {
        if ($receipts instanceof Receipt) {
            if ($receipts->isInvoiceReady() == false) {
                throw new InvoiceReceiptException(trans('receipt.not_billable'));
            }

            $this->receipts()->save($receipts);
            $this->recalculateAmount();

            return $receipts;
        } else {
            foreach ($receipts as $receipt) {
                if ($receipt->isInvoiceReady() == false) {
                    throw new InvoiceReceiptException(trans('receipt.has_not_billable'));
                }
            }

            $this->receipts()->saveMany($receipts);
            $this->recalculateAmount();

            return $receipts->count();
        }
    }

    public function removeReceipt($receipts)
    {
        if ($receipts instanceof Receipt) {
            DB::beginTransaction();
            $receipts->forceFill(['invoice_id' => null])->save();

            $this->recalculateAmount();
            DB::commit();

            return $receipts;
        } else {
            DB::beginTransaction();
            foreach ($receipts as $receipt) {
                $receipt->forceFill(['invoice_id' => null])->save();
            }

            $this->recalculateAmount();
            DB::commit();

            return $receipts->count();
        }
    }

    public function send()
    {
        if ($this->receipts()->count()) {
            $this->forceFill(['sent_date' => date('Y-m-d')])->save();
        }

        return false;
    }

    public function takeBack()
    {
        $this->forceFill(['sent_date' => null])->save();
    }

    public function setProblemDate($date)
    {
        $this->forceFill(['problem_date' => $date])->save();
    }

    public function isProblem()
    {
        return !!$this->problem_date;
    }

    public function scopeIsProblem($query)
    {
        return $query->whereNotNull('problem_date');
    }

    public function setPaymentDate($date)
    {
        $this->forceFill(['payment_date' => $date])->save();
    }

    public function verify()
    {
        DB::transaction(function () {
            $this->forceFill(['verify_date' => date('Y-m-d'), 'handler_id' => auth()->id()])->save();
            DB::table('transactions')
                ->where('invoice_id', $this->id)
                ->update([
                    'handler_id'  => auth()->id(),
                    'verified_at' => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
        });
    }

    public function delete()
    {
        DB::transaction(function () {
            $receiptIds = $this->receipts()->update(['invoice_id' => null]);
            parent::delete();
        });
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class);
    }

    public function addPayment()
    {
        $transaction = new Transaction;
        $transaction->number = $transaction->generateNumber();
        $transaction->date = \Carbon\Carbon::now();
        $transaction->in_out = 1;
        $transaction->amount = $this->receipts->sum('bill_amount');
        $transaction->creator_id = auth()->id();
        $transaction->handler_id = auth()->id();
        $transaction->verified_at = \Carbon\Carbon::now();
        $this->payments()->save($transaction);
    }

    public function recalculateAmount()
    {
        $this->amount = $this->getAmount();
        $this->save();

        return $this;
    }

    public function getAmount()
    {
        $amount = $this->receipts->sum('bill_amount')
         - $this->charge_details['discount']
         + $this->charge_details['admin_fee'];

        if ($this->type_id == 2 && $this->customer->is_taxed) {
            $taxAmount = 0.011 * $amount;
            $amount += $taxAmount;
        }

        return $amount;
    }
}
