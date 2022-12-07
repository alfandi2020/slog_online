<?php

namespace App\Entities\Networks;

use App\Entities\ReferenceAbstract;

class Type extends ReferenceAbstract
{
    protected static $lists = [
        1 => 'province',
        'city',
        'district',
        'outlet',
    ];

    public static function getNameById($singleId)
    {
        return trans('network.' . static::$lists[$singleId]);
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value)
            $lists[$key] = trans('network.' . $value);

        return $lists;
    }

    public static function all()
    {
        return collect($this->toArray());
    }

    public static function dropdown()
    {
        $lists = [];
        foreach (static::$lists as $key => $value)
            $lists[$key] = trans('network.' . $value);

        return $lists;
    }
}
