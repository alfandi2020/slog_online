<?php

namespace App\Entities\Networks;

use App\Entities\ReferenceAbstract;

class UnitType extends ReferenceAbstract
{
    protected static $lists = [
        1 => 'pick_up',
        'minibus',
        'motorcycle',
        'van',
        'truck',
    ];

    public static function getNameById($singleId)
    {
        return trans('delivery_unit.' . static::$lists[$singleId]);
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value)
            $lists[$key] = trans('delivery_unit.' . $value);

        return $lists;
    }

    public static function all()
    {
        return collect($this->toArray());
    }

    public static function dropdown()
    {
        return static::toArray();
    }
}
