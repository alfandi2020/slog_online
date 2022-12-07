<?php

namespace App\Entities\References;

use App\Entities\Customers\Customer;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model {

    protected $fillable = ['code','name','cat'];
    protected $table = 'site_references';
	public $timestamps = false;

    public function customers()
    {
        return $this->hasMany(Customer::class, 'comodity_id');
    }

    public function scopeComodity($query)
    {
        return $query->whereCat('comodity');
    }
}
