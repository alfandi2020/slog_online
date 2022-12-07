<?php

namespace App\Entities\Transactions;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];
}
