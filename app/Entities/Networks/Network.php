<?php

namespace App\Entities\Networks;

use App\Entities\Users\User;
use App\Entities\Regions\City;
use App\Entities\Networks\Type;
use App\Entities\Receipts\Receipt;
use App\Entities\Regions\District;
use App\Entities\Customers\Customer;
use App\Entities\Networks\DeliveryUnit;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = [
        'type_id', 'network_id', 'code', 'name', 'address',
        'coordinate', 'postal_code', 'phone', 'email',
        'origin_city_id', 'origin_district_id',
    ];

    public function nameLink()
    {
        return link_to_route('admin.networks.show', $this->code.' - '.$this->name, [$this->id], [
            'title' => trans(
                'app.show_detail_title',
                ['name' => $this->code.' - '.$this->name, 'type' => trans('network.network')]
            ),
        ]);
    }

    public function getCodeNameAttribute()
    {
        return $this->code.' - '.$this->name;
    }

    public function cityOrigin()
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function districtOrigin()
    {
        if (is_null($this->origin_district_id)) {
            return null;
        }

        return $this->belongsTo(District::class, 'origin_district_id');
    }

    public function origin()
    {
        if ($this->origin_district_id) {
            return $this->belongsTo(District::class, 'origin_district_id');
        }

        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function fullOriginName($format = null)
    {
        $fullOriginName = '';

        if ($this->origin_district_id) {
            $fullOriginName .= $this->districtOrigin->name;
            $fullOriginName .= $format == 'list' ? '<br>' : ' - ';
        }

        $fullOriginName .= $this->cityOrigin->name;
        $fullOriginName .= $format == 'list' ? '<br>' : ' - ';
        $fullOriginName .= $this->cityOrigin->province->name;

        return $fullOriginName;
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function deliveryUnits()
    {
        return $this->hasMany(DeliveryUnit::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function type()
    {
        return Type::getNameById($this->type_id);
    }
}
