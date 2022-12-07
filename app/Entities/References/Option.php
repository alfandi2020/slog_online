<?php

namespace App\Entities\References;

use Illuminate\Database\Eloquent\Model;

class Option extends Model {

    protected $fillable = ['key','value','user_id'];
    protected $table = 'site_options';
	public $timestamps = false;
}
