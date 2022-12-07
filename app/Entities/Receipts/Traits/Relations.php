<?php

namespace App\Entities\Receipts\Traits;

use App\Entities\Customers\Customer;
use App\Entities\Invoices\Invoice;
use App\Entities\Manifests\Manifest;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Progress;
use App\Entities\References\Reference;
use App\Entities\Services\Rate;
use App\Entities\Users\User;

trait Relations
{

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

    public function setRate(Rate $rate)
    {
        $this->rate_id = $rate->id;
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryCourier()
    {
        return $this->belongsTo(User::class);
    }

    public function pickupCourier()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryCourierName()
    {
        return $this->delivery_courier_id ? $this->deliveryCourier->name : null;
    }

    public function packType()
    {
        return $this->belongsTo(Reference::class, 'pack_type_id')->where('cat', 'pack_type');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function lastProgress()
    {
        return $this->hasOne(Progress::class)->latest();
    }

    public function manifests()
    {
        return $this->belongsToMany(Manifest::class, 'receipt_progress')->withPivot(['handler_id', 'updated_at', 'notes']);
    }

    public function distributionManifest()
    {
        return $this->manifests()->where('type_id', 3)->latest()->first();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getInvoiceNumber()
    {
        return $this->invoice_id ? $this->invoice->number : '-';
    }

    public function getInvoiceLink()
    {
        return $this->invoice_id ? link_to_route('invoices.show', $this->invoice->number, [$this->invoice->id], ['target' => '_blank']) : '-';
    }
}
