<?php

namespace App\Entities\Manifests;

use App\Entities\Receipts\Receipt;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;

class Problem extends Manifest
{
    protected $table = 'manifests';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('problem_type', function (Builder $builder) {
            $builder->where('type_id', 6);
        });
    }

    public function setTypeIdAttribute($value)
    {
        $this->attributes['type_id'] = 6;
    }

    /**
     * Manifest Belongs To Many Receipt Relation
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receipts()
    {
        return $this->belongsToMany(Receipt::class, 'receipt_progress', 'manifest_id')
            ->orderBy('receipts.number', 'asc')
            ->withPivot(['id', 'creator_id', 'handler_id', 'end_status', 'notes'])
            ->withTimestamps();
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

        if ($this->type_id == 6 && $receipt->hasStatusOf(['no'])) { // problem
            $statusCode = 'pr';
        }

        if (is_null($statusCode)) {
            return false;
        }

        $creatorId = auth()->id() ?: $receipt->creator_id;

        $this->receipts()->attach($receipt, [
            'start_status' => $statusCode,
            'creator_id' => $creatorId,
            'creator_location_id' => auth()->check() ? auth()->user()->network->origin_city_id : $receipt->orig_city_id,
        ]);

        DB::commit();

        return true;
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

        $this->receipts->each->setStatusCode('pr');
        event(new \App\Events\Manifests\ProblemSent($this));

        $this->forceFill(['deliver_at' => Carbon::now()])->save();
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

        DB::beginTransaction();
        $this->receipts->each->setStatusCode('pd');
        event(new \App\Events\Manifests\ProblemReceived($this));

        $progressIds = $this->receipts->pluck('pivot.id')->all();
        DB::table('receipt_progress')->whereIn('id', $progressIds)->update([
            'handler_id' => auth()->id(),
            'handler_location_id' => auth()->user()->network->origin->id,
            'end_status' => 'pd',
            'updated_at' => Carbon::now(),
        ]);

        $this->forceFill([
            'handler_id' => auth()->id(),
            'received_at' => Carbon::now()
        ])->save();

        DB::commit();

        return $this;
    }

    /**
     * Type of manifest
     * @return string Manifest type name
     */
    public function type()
    {
        return Type::getNameById(6);
    }

    /**
     * Type color of manifest
     * @return string Manifest type color
     */
    public function typeColor()
    {
        return Type::getColorById(6);
    }

    /**
     * Type code of manifest
     * @return string Manifest type code
     */
    public function typeCode()
    {
        return Type::getById(6);
    }

    /**
     * Plural Type Code of manifest
     * @return string Pluralized manifest type code
     */
    public function pluralTypeCode()
    {
        return Type::getPluralCodeById(6);
    }
}
