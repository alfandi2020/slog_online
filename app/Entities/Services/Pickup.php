<?php

namespace App\Entities\Services;

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Networks\Network;
use App\Entities\Users\User;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $fillable = [
        'number', 'courier_id', 'creator_id',
        'delivery_unit_id', 'network_id',
        'date', 'customers', 'notes',
        'sent_at', 'returned_at',
        'start_km', 'end_km',
    ];

    protected $dates = ['sent_at', 'returned_at'];

    protected $casts = ['customers' => 'array'];

    public function numberLink()
    {
        return link_to_route('pickups.show', $this->number, [$this->id], [
            'title' => trans(
                'pickup.show_detail_title',
                ['number' => $this->number]
            ),
        ]);
    }

    public function generateNumber()
    {
        $prefix = 'PU'.date('ym');

        $lastPickup = $this->orderBy('number', 'desc')->first();

        if (!is_null($lastPickup)) {
            $lastPickupNo = $lastPickup->number;
            if (substr($lastPickupNo, 0, 6) == $prefix) {
                return ++$lastPickupNo;
            }
        }

        return $prefix.'0001';
    }

    public function courier()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryUnit()
    {
        return $this->belongsTo(DeliveryUnit::class);
    }

    public function getStatusAttribute()
    {
        if (!is_null($this->sent_at) && is_null($this->returned_at)) {
            return trans('pickup.on_pickup');
        }

        if (!is_null($this->sent_at) && !is_null($this->returned_at)) {
            return trans('pickup.returned');
        }

        return trans('pickup.data_entry');
    }

    public function getStatusLabelAttribute()
    {
        if (!is_null($this->sent_at) && is_null($this->returned_at)) {
            return '<span class="label label-info">'.trans('pickup.on_pickup').'</span>';
        }

        if (!is_null($this->sent_at) && !is_null($this->returned_at)) {
            return '<span class="label label-success">'.trans('pickup.returned').'</span>';
        }

        return '<span class="label label-default">'.trans('pickup.data_entry').'</span>';
    }

    public function getCustomersCountAttribute()
    {
        return count($this->customers);
    }

    public function getReceiptsCountAttribute()
    {
        $receiptsCount = 0;
        $customerPickups = $this->customers;
        foreach ($customerPickups as $customerId => $pickupData) {
            $receiptsCount += $pickupData['receipts_count'];
        }

        return $receiptsCount;
    }

    public function getPcsCountAttribute()
    {
        $pcsCount = 0;
        $customerPickups = $this->customers;
        foreach ($customerPickups as $customerId => $pickupData) {
            $pcsCount += $pickupData['pcs_count'];
        }

        return $pcsCount;
    }

    public function getItemsCountAttribute()
    {
        $itemsCount = 0;
        $customerPickups = $this->customers;
        foreach ($customerPickups as $customerId => $pickupData) {
            $itemsCount += $pickupData['items_count'];
        }

        return $itemsCount;
    }

    public function getWeightTotalAttribute()
    {
        $weightTotal = 0;
        $customerPickups = $this->customers;
        foreach ($customerPickups as $customerId => $pickupData) {
            $weightTotal += $pickupData['weight_total'];
        }

        return $weightTotal;
    }

    public function isDataEntry()
    {
        return is_null($this->sent_at) && is_null($this->returned_at);
    }

    public function isOnPickup()
    {
        return !is_null($this->sent_at) && is_null($this->returned_at);
    }

    public function hasReturned()
    {
        return !is_null($this->sent_at) && !is_null($this->returned_at);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function getBarcodeAttribute()
    {
        return \Html::image(url('barcode/img/'.$this->number.'/25'));
    }
}
