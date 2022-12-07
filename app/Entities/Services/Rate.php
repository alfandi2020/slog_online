<?php

namespace App\Entities\Services;

use App\Entities\Customers\Customer;
use App\Entities\References\Reference;
use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Regions\Province;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'customer_id', 'service_id', 'orig_city_id', 'orig_district_id',
        'dest_city_id', 'dest_district_id', 'rate_kg', 'rate_pc',
        'min_weight', 'discount', 'add_cost', 'etd', 'notes'
    ];

    protected $appends = ['orig_prov_id','dest_prov_id'];

    public function getOrigProvIdAttribute()
    {
        return substr($this->orig_city_id, 0, 2);
    }

    public function getDestProvIdAttribute()
    {
        return substr($this->dest_city_id, 0, 2);
    }

    public function originProvinceName()
    {
        return $this->originProvince->name;
    }

    public function originProvince()
    {
        return $this->belongsTo(Province::class, 'orig_prov_id');
    }

    public function destinationProvinceName()
    {
        return $this->destinationProvince->name;
    }

    public function destinationProvince()
    {
        return $this->belongsTo(Province::class, 'dest_prov_id');
    }

    public function service()
    {
        return strtoupper(Service::getById($this->service_id));
    }

    public function packType()
    {
        return $this->belongsTo(Reference::class, 'pack_type_id')->where('cat', 'pack_type');
    }

    public function packTypeName()
    {
        if ($this->pack_type_id == 1)
            return 'Paket';

        return $this->packType->name;
    }

    public function origin()
    {
        if ($this->orig_district_id)
            return $this->belongsTo(District::class, 'orig_district_id');

        return $this->belongsTo(City::class, 'orig_city_id');
    }

    public function originName()
    {
        return $this->origin->name;
    }

    public function destination()
    {
        if ($this->dest_district_id)
            return $this->belongsTo(District::class, 'dest_district_id');

        return $this->belongsTo(City::class, 'dest_city_id');
    }

    public function cityDestination()
    {
        return $this->belongsTo(City::class, 'dest_city_id');
    }

    public function districtDestination()
    {
        return $this->belongsTo(District::class, 'dest_district_id');
    }

    public function cityOrigin()
    {
        return $this->belongsTo(City::class, 'orig_city_id');
    }

    public function districtOrigin()
    {
        return $this->belongsTo(District::class, 'orig_district_id');
    }

    public function destinationName()
    {
        return $this->destination->name;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
