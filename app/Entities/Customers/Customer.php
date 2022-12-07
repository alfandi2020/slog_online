<?php

namespace App\Entities\Customers;

use App\Entities\Services\Rate;
use App\Entities\Invoices\Invoice;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\References\Reference;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class Customer extends Model
{
    use PresentableTrait;

    protected $presenter = CustomerPresenter::class;
    protected $casts = [
        'pic'     => 'array',
        'address' => 'array',
    ];

    public function comodity()
    {
        return $this->belongsTo(Reference::class)->where('cat', 'comodity');
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function isTaxed()
    {
        return $this->is_taxed ? trans('app.yes') : trans('app.no');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function invoicedReceipts()
    {
        return $this->hasMany(Receipt::class)->whereNotNull('invoice_id');
    }

    public function unInvoicedReceipts()
    {
        return $this->hasMany(Receipt::class)->whereNull('invoice_id');
    }

    public function invoiceReadyReceipts()
    {
        return $this->hasMany(Receipt::class)
            ->whereNull('invoice_id')
            ->where('status_code', 'ir');
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getCategoryAttribute()
    {
        if (in_array($this->category_id, [1, 2, 3])) {
            return __('customer.category_'.$this->category_id);
        }
    }

    public function getPodChecklistAttribute()
    {
        return config('bam-cargo.pod_checklist.category_'.$this->category_id);
    }

    public function getPodChecklistDisplayAttribute()
    {
        return implode('<br>', array_map(function ($item) {
            return '[ ] '.$item;
        }, config('bam-cargo.pod_checklist.category_'.$this->category_id)));
    }
}
