<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class ReceiptCollection extends Facade
{
    protected static function getFacadeAccessor() { return 'receiptCollection'; }
}