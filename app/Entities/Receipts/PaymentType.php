<?php

namespace App\Entities\Receipts;

use App\Entities\ReferenceAbstract;
use Illuminate\Support\Arr;

/**
 * Receipt Status Collection Class
 */
class PaymentType extends ReferenceAbstract
{
    protected static $lists = [
        1 => 'cash',
        2 => 'credit',
        3 => 'cod',
    ];

    protected static $colors = [
        1 => 'lightgreen',
        2 => 'pink',
        3 => 'lightblue',
    ];

    public static function getNameById($singleId)
    {
        return isset(static::$lists[$singleId]) ? __('receipt.payment_types.'.static::$lists[$singleId]) : null;
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value) {
            $lists[$key] = __('receipt.payment_types.'.$value);
        }

        return $lists;

    }

    public static function toArrayNoCredit()
    {
        $lists = [];
        foreach (static::$lists as $key => $value) {
            $lists[$key] = __('receipt.payment_types.'.$value);
        }

        return $filteredLists = Arr::except($lists, [2]);
    }

    public static function dropdown()
    {
        return ['' => '-- Semua --']+static::toArray();
    }
}
