<?php

namespace App\Entities\Receipts;

use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Users\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Proof Model, the proof of delivery (POD).
 *
 * @author Nafies Luthfi <nafiesl@gmail.com>
 */
class Proof extends Model
{
    protected $table = 'delivery_proofs';

    protected $dates = ['delivered_at'];

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        if (strlen($this->location_id) == 7) {
            return $this->belongsTo(District::class);
        }

        return $this->belongsTo(City::class);
    }
}
