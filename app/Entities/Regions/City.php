<?php

namespace App\Entities\Regions;

use App\Entities\Regions\District;
use App\Entities\Services\Rate;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function destinationCities()
    {
        return $this->belongsToMany(City::class, 'rates', 'orig_city_id', 'dest_city_id')->where('rates.customer_id', 0);
    }

    public function destinationDistricts()
    {
        return $this->belongsToMany(District::class, 'rates', 'orig_city_id', 'dest_district_id')->where('rates.customer_id', 0);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'dest_city_id');
    }

    public function retailRates()
    {
        return $this->hasMany(Rate::class, 'dest_city_id')->where('customer_id', 0);
    }
}
