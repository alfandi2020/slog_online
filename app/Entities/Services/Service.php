<?php

namespace App\Entities\Services;

use App\Entities\ReferenceAbstract;

/**
 * Service Class
 */
class Service extends ReferenceAbstract
{
    public static $lists = [
        11 => 'exp',
        21 => 'reg',
        22 => 'eco',
        41 => 'brg',
    ];

    public static function getById($singleId)
    {
        if ($singleId && isset(static::$lists[$singleId])) {
            return static::$lists[$singleId];
        }

        return null;
    }

    public static function getNameById($singleId)
    {
        if ($singleId) {
            return __('service.'.static::$lists[$singleId]);
        }

        return null;
    }

    public static function getCodeNameById($singleId)
    {
        if ($singleId) {
            return static::$lists[$singleId].' ('.__('service.'.static::$lists[$singleId]).')';
        }

        return null;
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value) {
            $lists[$key] = __('service.'.$value);
        }

        return $lists;
    }

    public static function all()
    {
        return collect(static::toArray());
    }

    public static function retailDropdown()
    {
        $lists = [];
        foreach (static::only([21, 22, 11]) as $key => $value) {
            $lists[$key] = strtoupper($value).' ('.__('service.'.$value).')';
        }

        krsort($lists);
        return $lists;
    }

    public static function ratailAndSalDropdown()
    {
        $lists = [];
        foreach (static::only([21, 22, 11]) as $key => $value) {
            $lists[$key] = strtoupper($value).' ('.__('service.'.$value).')';
        }

        // krsort($lists);
        return $lists;
    }

    public static function ratailAndSalList()
    {
        $lists = static::only([21, 22, 11]);
        krsort($lists);

        return $lists;
    }

    public static function allRetailAndSalService()
    {
        $lists = static::only([21, 22, 11]);

        return $lists;
    }

    public static function dropdown()
    {
        $lists = [];
        foreach (static::$lists as $key => $value) {
            $lists[$key] = strtoupper($value).' - '.__('service.'.$value);
        }

        return $lists;
    }
}
