<?php

namespace App\Entities\Receipts;

use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Proof;
use App\Entities\Receipts\Status;
use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Users\User;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'receipt_progress';

    protected $fillable = [
        'receipt_id', 'manifest_id', 'creator_id',
        'creator_location_id', 'start_status', 'handler_id',
        'handler_location_id', 'end_status',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    public function proof()
    {
        return $this->hasOne(Proof::class);
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id');
    }

    public function origin()
    {
        if (strlen($this->creator_location_id) == 7) {
            return $this->belongsTo(District::class, 'creator_location_id');
        }

        return $this->belongsTo(City::class, 'creator_location_id');
    }

    public function destination()
    {
        if (strlen($this->handler_location_id) == 7) {
            return $this->belongsTo(District::class, 'handler_location_id');
        }

        return $this->belongsTo(City::class, 'handler_location_id');
    }

    public function openStatus()
    {
        return Status::getNameById($this->start_status);
    }

    public function openStatusLabel()
    {
        $labelType = 'default';
        return "<span class='label label-{$labelType}'>{$this->openStatus()}</span>";
    }

    public function closeStatus()
    {
        return Status::getNameById($this->end_status);
    }

    public function closeStatusLabel()
    {
        $labelType = in_array($this->end_status, ['bd', 'dl']) ? 'success' : 'default';
        return "<span class='label label-{$labelType}'>{$this->closeStatus()}</span>";
    }

    public function isOpen()
    {
        return !!is_null($this->handler_id);
    }

    public function isClosed()
    {
        return !$this->isOpen();
    }
}
