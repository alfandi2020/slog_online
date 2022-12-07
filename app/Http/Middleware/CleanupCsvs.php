<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class CleanupCsvs extends TransformsRequest
{
    /**
     * The attributes that should not be transformed.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (in_array($key, $this->except, true)) {
            return $value;
        }

        if (is_string($value)) {
            $value = preg_replace(['/,/', '/,\s+/'], ', ', $value);
        }

        return $value;
    }
}