<?php

namespace App\Entities;

use App\Entities\Regions\City;
use App\Entities\Networks\Network;
use App\Entities\Regions\Province;
use App\Entities\Customers\Customer;

/**
 * Base Repository Class
 */
abstract class BaseRepository extends EloquentRepository
{
    public function getNetworksList()
    {
        return Network::pluck('name', 'id');
    }

    public function getProvinceById($provinceId)
    {
        return Province::find($provinceId);
    }

    public function getCityById($cityId)
    {
        return City::find($cityId);
    }

    public function getCustomersDropdown()
    {
        return ['Customer Umum'] + Customer::pluck('name', 'id')->all();
    }
}
