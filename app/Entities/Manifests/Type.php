<?php

namespace App\Entities\Manifests;

use App\Entities\ReferenceAbstract;

class Type extends ReferenceAbstract
{
    protected static $lists = [
        1 => 'handover',
        'delivery',
        'distribution',
        'return',
        'accounting',
        'problem',
    ];

    protected static $colors = [
        1 => '#777',
        '#8eb4cb',
        '#2ab27b',
        '#cbb956',
        '#f0ad4e',
        '#EF3030',
    ];

    public static function getById($singleId)
    {
        return isset(static::$lists[$singleId]) ? static::$lists[$singleId] : null;
    }

    public static function getPluralCodeById($singleId)
    {
        return isset(static::$lists[$singleId]) ? str_plural(static::$lists[$singleId]) : null;
    }

    public static function getNameById($singleId)
    {
        return isset(static::$lists[$singleId]) ? trans('manifest.' . static::$lists[$singleId]) : null;
    }

    public static function toArray()
    {
        $lists = [];
        foreach (static::$lists as $key => $value)
            $lists[$key] = trans('manifest.' . $value);

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
            $lists[$key] = trans('manifest.' . $value);

        return $lists;
    }
}
