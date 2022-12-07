<?php

namespace App\Entities\Manifests;

use DB;
use Carbon\Carbon;
use App\Entities\Users\User;
use App\Entities\Regions\City;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Receipts\Progress;
use App\Entities\Customers\Customer;
use App\Entities\Networks\DeliveryUnit;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class Manifest extends Model
{
    use PresentableTrait;
    protected $presenter = ManifestPresenter::class;
    protected $dates = ['deliver_at', 'received_at'];

    public function getRouteKeyName()
    {
        return 'number';
    }

    public function originNetwork()
    {
        return $this->belongsTo(Network::class, 'orig_network_id');
    }

    public function originName()
    {
        return $this->originNetwork->name;
    }

    public function destinationNetwork()
    {
        return $this->belongsTo(Network::class, 'dest_network_id');
    }

    public function destinationName()
    {
        return $this->destinationNetwork->name;
    }

    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'dest_city_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    /**
     * A manifest can be belongs to a DeliveryUnit
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryUnit()
    {
        return $this->belongsTo(DeliveryUnit::class);
    }

    public function deliveryCourier()
    {
        return $this->belongsTo(User::class, 'delivery_unit_id')->withDefault(['name' => '-']);
    }

    /**
     * Add receipt to current manifest
     * @param Receipt $receipt  Existing receipt on database
     * @return boolean          Weather receipt added or not
     */
    public function addReceipt(Receipt $receipt)
    {
        if ($receipt->isOnManifestProgress()) {
            return false;
        }

        if ($this->receipts->contains($receipt->id)) {
            return false;
        }

        DB::beginTransaction();
        $statusCode = null;

        if ($this->type_id == 1 && $receipt->hasStatusOf(['de', 'pd'])) // handover
        {
            $statusCode = 'mw';
        } elseif ($this->type_id == 2 && $receipt->hasStatusOf(['de', 'rw', 'rd'])) // delivery
        {
            $statusCode = 'mn';
        } elseif ($this->type_id == 3 && $receipt->hasStatusOf(['de', 'rw', 'rd', 'pd'])) // distribution
        {
            $statusCode = 'od';
        } elseif ($this->type_id == 3 && $receipt->hasStatusOf([ // distribution
            'au', 'mr', 'o1', 'o2', 'o3', 'o4', 'o5', 'o6', 'o7', 'o8', 'o9', 'o0',
        ])) {
            $statusCode = 'od';
        } elseif ($this->type_id == 4 && $receipt->hasStatusOf(['dl', 'bd']) && $this->dest_network_id == $receipt->network_id) // return
        {
            $statusCode = 'or';
        } elseif ($this->type_id == 5 && $receipt->hasStatusOf(['dl', 'bd', 'rt', 'ma', 'pd'])) // accounting
        {
            $statusCode = 'ma';
        } elseif ($this->type_id == 6 && $receipt->hasStatusOf(['no'])) // problem
        {
            $statusCode = 'pr';
        }

        if (is_null($statusCode)) {
            return false;
        }

        $creatorId = auth()->id() ?: $receipt->creator_id;

        // If distribution manifest and destination city is different
        if ($this->type_id == 3 && $this->dest_city_id !== $receipt->dest_city_id) {
            return false;
        }

        // If distribution manifest, than progress creator is the courier
        if ($this->type_id == 3) {
            $creatorId = $this->handler_id;
            if ($receipt->status_code == 'rt') {
                \Log::warning('Returned Receipt On Delivery Distribution', [
                    'data' => [
                        'user_id'         => auth()->id(),
                        'user_name'       => auth()->user()->name,
                        'receipt_id'      => $receipt->id,
                        'receipt_number'  => $receipt->number,
                        'status_code'     => $receipt->status_code,
                        'status_become'   => $statusCode,
                        'manifest_id'     => $this->id,
                        'manifest_number' => $this->number,
                    ],
                ]);
            }
        }

        $this->receipts()->attach($receipt, [
            'start_status'        => $statusCode,
            'creator_id'          => $creatorId,
            'creator_location_id' => auth()->check() ? auth()->user()->network->origin_city_id : $receipt->orig_city_id,
        ]);

        DB::commit();

        return true;
    }

    /**
     * Remove a receipt from current manifest
     * @param  Receipt $receipt Existing receipt in database
     * @return boolean          Weather receipt has removed or not
     */
    public function removeReceipt(Receipt $receipt)
    {
        $result = $this->receipts()->detach($receipt);
        return (bool) $result;
    }

    /**
     * Check receipt that exists on a receivable manifest
     * @param  string $receiptNumber Number of receipt on manifest
     * @return boolean               false if not found | true if receipt is exists and status is updated
     */
    public function checkReceipt(string $receiptNumber)
    {
        $receipt = $this->receipts()->where('number', $receiptNumber)->first();
        if (is_null($receipt)) {
            return false;
        }

        DB::beginTransaction();

        $user = auth()->user();
        $progress = Progress::findOrFail($receipt->pivot->id);
        $progress->handler_id = $user ? $user->id : null;
        $progress->handler_location_id = $user ? $user->network->origin->id : null;

        if ($this->type_id == 1) {
            $receipt->setStatusCode('rw');
            $progress->end_status = 'rw';
        } elseif ($this->type_id == 2) {
            $receipt->setStatusCode('rd');
            $progress->end_status = 'rd';
        } elseif ($this->type_id == 4) {
            $receipt->setStatusCode('rt');
            $progress->end_status = 'rt';
        } elseif ($this->type_id == 5) {
            $receipt->setStatusCode('ir');
            $progress->end_status = 'ir';
        }

        $progress->save();

        DB::commit();

        return true;
    }

    /**
     * Reject receipt that exists on a receivable manifest
     * @param  string $receiptNumber Number of receipt on manifest
     * @return boolean               false if not found | true if receipt is exists and status is updated
     */
    public function rejectReceipt(string $receiptNumber)
    {
        $receipt = $this->receipts()->where('number', $receiptNumber)->first();
        if (is_null($receipt)) {
            return false;
        }

        DB::beginTransaction();

        $user = auth()->user();
        $progress = Progress::findOrFail($receipt->pivot->id);
        $progress->handler_id = $user ? $user->id : null;
        $progress->handler_location_id = $user ? $user->network->origin->id : null;

        $receipt->setStatusCode('no');
        $progress->end_status = 'no';

        $progress->save();

        DB::commit();

        return true;
    }

    /**
     * Manifest Belongs To Many Receipt Relation
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receipts()
    {
        return $this->belongsToMany(Receipt::class, 'receipt_progress')
            ->orderBy('receipts.number', 'asc')
            ->withPivot(['id', 'creator_id', 'handler_id', 'end_status', 'notes'])
            ->withTimestamps();
    }

    /**
     * Send manifest to Destination Network
     * @return false|App\Entities\Manifests\Manifest
     */
    public function send()
    {
        if ($this->isSent() || $this->receipts()->count() == 0) {
            return false;
        }

        if ($this->type_id == 1) {
            $this->receipts->each->setStatusCode('mw');
            event(new \App\Events\Manifests\HandoverSent($this));
        } elseif ($this->type_id == 2) {
            $this->receipts->each->setStatusCode('mn');
            event(new \App\Events\Manifests\DeliverySent($this));
        } elseif ($this->type_id == 3) {
            $this->receipts->each->setStatusCode('od');
        } elseif ($this->type_id == 4) {
            $this->receipts->each->setStatusCode('or');
            event(new \App\Events\Manifests\ReturnSent($this));
        } elseif ($this->type_id == 5) {
            $this->receipts->each->setStatusCode('ma');
            event(new \App\Events\Manifests\AccountingSent($this));
        } elseif ($this->type_id == 6) {
            $this->receipts->each->setStatusCode('pr');
            event(new \App\Events\Manifests\ProblemSent($this));
        }

        $this->forceFill(['deliver_at' => Carbon::now()])->save();
        return $this;
    }

    /**
     * Take manifest back to Origin Network
     * @return false|App\Entities\Manifests\Manifest
     */
    public function takeBack()
    {
        if ($this->isNotSent()) {
            return false;
        }

        if ($this->type_id == 1) {
            $this->receipts->each->setStatusCode('de');
        } elseif ($this->type_id == 2) {
            $this->receipts->each->setStatusCode('rw');
        } elseif ($this->type_id == 3) {
            // $this->receipts->each->setStatusCode('od');
        } elseif ($this->type_id == 4) {
            // $this->receipts->each->setStatusCode('or');
        } elseif ($this->type_id == 5) {
            // $this->receipts->each->setStatusCode('ma');
        }

        $this->forceFill(['deliver_at' => null])->save();
        return $this;
    }

    /**
     * Receive manifest from Origin Network
     * @return false|App\Entities\Manifests\Manifest
     */
    public function receive()
    {
        if ($this->isNotSent()) {
            return false;
        }

        if ($this->type_id == 1) {
            event(new \App\Events\Manifests\HandoverReceived($this));
        } elseif ($this->type_id == 2) {
            event(new \App\Events\Manifests\DeliveryReceived($this));
        } elseif ($this->type_id == 3) {
        } elseif ($this->type_id == 4) {
            $this->receipts->each->setStatusCode('rt');
            event(new \App\Events\Manifests\ReturnReceived($this));
        } elseif ($this->type_id == 5) {
            // $this->receipts->each->setStatusCode('ir');
            event(new \App\Events\Manifests\AccountingReceived($this));
        }

        $this->forceFill([
            'handler_id'  => auth()->id(),
            'received_at' => Carbon::now(),
        ])->save();

        return $this;
    }

    /**
     * Determine if manifest is not sent already
     * @return boolean
     */
    public function isNotSent()
    {
        return is_null($this->deliver_at);
    }

    /**
     * Determine if manifest is already sent
     * @return boolean
     */
    public function isSent()
    {
        return !$this->isNotSent();
    }

    /**
     * Determine if manifest is already received by Destination Network
     * @return boolean
     */
    public function isReceived()
    {
        return !is_null($this->received_at);
    }

    /**
     * Determine if manifest receipts are all delivered
     * @return boolean
     */
    public function isAllDelivered()
    {
        return !!$this->receipts->each(function ($receipt) {
            return $receipt->isDelivered();
        })->count();
    }

    /**
     * Manifest and it's user creator relation
     * @return App\Entities\Users\User
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Manifest and it's user handler relation (on received)
     * @return App\Entities\Users\User
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    /**
     * Type of manifest
     * @return string Manifest type name
     */
    public function type()
    {
        return Type::getNameById($this->type_id);
    }

    /**
     * Type color of manifest
     * @return string Manifest type color
     */
    public function typeColor()
    {
        return Type::getColorById($this->type_id);
    }

    /**
     * Type code of manifest
     * @return string Manifest type code
     */
    public function typeCode()
    {
        return Type::getById($this->type_id);
    }

    /**
     * Plural Type Code of manifest
     * @return string Pluralized manifest type code
     */
    public function pluralTypeCode()
    {
        return Type::getPluralCodeById($this->type_id);
    }

    /**
     * Determine if manifest is type of given parameter
     * @return boolean
     */
    public function isTypeOf($type)
    {
        if (is_string($type)) {
            return $this->typeCode() == $type;
        }

        return in_array($this->typeCode(), $type);
    }

    public static function findByNumber($number)
    {
        return static::where('number', $number)->first();
    }

    /**
     * Query Scope to filter distribution manifest only
     * @param  Illuminate\Database\Query\Builder $query The query builder
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeIsDistribution($query)
    {
        return $query->where('type_id', 3);
    }
}
