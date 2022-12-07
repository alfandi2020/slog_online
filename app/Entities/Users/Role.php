<?php

namespace App\Entities\Users;

use App\Entities\ReferenceAbstract;

/**
 * Role Class
 */
class Role extends ReferenceAbstract
{
    protected static $lists = [
        1 => 'admin',
        2 => 'accounting',
        3 => 'sales_counter',
        4 => 'warehouse',
        5 => 'cs',
        6 => 'cashier',
        7 => 'courier',
        9 => 'branch_head',
    ];

    public static function getById($singleId)
    {
        return trans('user.'.static::$lists[$singleId]);
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value) {
            $lists[$key] = trans('user.'.$value);
        }

        return $lists;
    }

    public static function all()
    {
        return collect($this->toArray());
    }

    public static function dropdown()
    {
        $lists = [];
        foreach (static::$lists as $key => $value) {
            $lists[$key] = strtoupper($value).' - '.trans('user.'.$value);
        }

        return $lists;
    }
}
