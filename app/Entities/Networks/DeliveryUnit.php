<?php

namespace App\Entities\Networks;

use Illuminate\Database\Eloquent\Model;

class DeliveryUnit extends Model
{
    protected $fillable = [
        'name', 'description', 'plat_no',
        'type_id', 'network_id', 'is_active',
    ];

    public function getTypeAttribute()
    {
        return UnitType::getNameById($this->type_id);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
