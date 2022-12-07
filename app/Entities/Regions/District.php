<?php

namespace App\Entities\Regions;

use App\Entities\Services\Rate;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'dest_district_id');
    }

    public function getNameAttribute($name)
    {
        return 'Kec. ' . $name;
    }
}
