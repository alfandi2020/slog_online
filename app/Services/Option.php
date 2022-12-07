<?php

namespace App\Services;

use App\Entities\References\Option as SiteOption;
use Cache;

/**
* Option Class (Site Option Service)
*/
class Option
{

    protected $option;

    public function __construct()
    {
        // Cache::forget('option_all');
        if (Cache::has('option_all')) {
            $this->option = Cache::get('option_all');
        } else {
            $this->option = SiteOption::all();
            Cache::put('option_all', $this->option, 60);
        }
    }

    public function get($key, $default = '')
    {
        $option = $this->option->where('key', $key)->first();
        return $option ? $option->value : $default;
    }
}