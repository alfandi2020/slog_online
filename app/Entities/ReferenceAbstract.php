<?php

namespace App\Entities;

use Illuminate\Support\Arr;

abstract class ReferenceAbstract
{
    protected static $lists = [];

    protected static $colors = [];

    public static function all()
    {
        return collect(static::$lists);
    }

    public static function toArray()
    {
        return static::$lists;
    }

    public static function getById($singleId)
    {
        return static::$lists[$singleId];
    }

    public static function only(array $singleIds)
    {
        return Arr::only(static::$lists, $singleIds);
    }

    public static function except($singleId)
    {
        return Arr::except(static::$lists, [$singleId]);
    }

    public static function colors()
    {
        return static::$colors;
    }

    public static function getColorById($colorId)
    {
        return isset(static::$lists[$colorId]) ? static::$colors[$colorId] : null;
    }

    public static function colorsExcept($colorId)
    {
        return Arr::except(static::$colors, [$colorId]);
    }

    public static function colorsOnly($colorIds = [])
    {
        return Arr::only(static::$colors, $colorIds);
    }
}
