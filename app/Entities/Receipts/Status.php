<?php

namespace App\Entities\Receipts;

use App\Entities\ReferenceAbstract;

/**
* Receipt Status Collection Class
*/
class Status extends ReferenceAbstract
{
    protected static $lists = [
            // Delivery
            'de' => 'de',
            'mw' => 'mw',
            'rw' => 'rw',
            'mn' => 'mn',
            'ot' => 'ot',
            'rd' => 'rd',
            'od' => 'od',
            'no' => 'no',
            'pr' => 'pr',
            'pd' => 'pd',

            // Prove of Delivery
            'dl' => 'dl',
            'bd' => 'bd',
            'au' => 'au',
            'mr' => 'mr',
            'o1' => 'o1',
            'o2' => 'o2',
            'o3' => 'o3',
            'o4' => 'o4',
            'o5' => 'o5',
            'o6' => 'o6',
            'o7' => 'o7',
            'o8' => 'o8',
            'o9' => 'o9',
            'o0' => 'o0',

            // After Delivery
            'or' => 'or',
            'rt' => 'rt',
            'ma' => 'ma',
            'ir' => 'ir',
            'id' => 'id',
    ];

    protected static $pod = [
        'dl' => 'dl',
        'bd' => 'bd',
        'au' => 'au',
        'mr' => 'mr',
        'o1' => 'o1',
        'o2' => 'o2',
        'o3' => 'o3',
        'o4' => 'o4',
        'o5' => 'o5',
        'o6' => 'o6',
        'o7' => 'o7',
        'o8' => 'o8',
        'o9' => 'o9',
        'o0' => 'o0',
    ];

    protected static $public = [
        'de' => 'de',
        'mw' => 'mw',
        'rw' => 'rw',
        'mn' => 'mn',
        'rd' => 'rd',
        'od' => 'od',
        'dl' => 'dl',
        'bd' => 'bd',
        'au' => 'au',
        'mr' => 'mr',
    ];

    protected static $delivery = [
        'de' => 'de',
        'mw' => 'mw',
        'rw' => 'rw',
        'mn' => 'mn',
        'rd' => 'rd',
        'od' => 'od',
    ];

    protected static $invoiceable = [
        'dl' => 'dl',
        'bd' => 'bd',
        'or' => 'or',
        'rt' => 'rt',
        'ma' => 'ma',
        'ir' => 'ir',
    ];

    public static function getNameById($singleId)
    {
        return isset(static::$lists[$singleId]) ? trans('receipt_status.' . static::$lists[$singleId]) : null;
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value)
            $lists[$key] = trans('receipt_status.' . $value);

        return $lists;
    }

    public static function podDropdown()
    {
        $lists = [];
        foreach (static::$pod as $key => $value)
            $lists[$key] = trans('receipt_status.' . $value);

        return $lists;
    }

    public static function publicList()
    {
        $lists = [];
        foreach (static::$public as $key => $value)
            $lists[$key] = trans('receipt_status.' . $value);

        return $lists;
    }

    public static function getList($type)
    {
        $lists = [];
        foreach (static::${$type} as $key => $value)
            $lists[$key] = trans('receipt_status.' . $value);

        return $lists;
    }
}