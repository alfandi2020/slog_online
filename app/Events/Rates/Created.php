<?php

namespace App\Events\Rates;

use App\Entities\Services\Rate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class Created
{
    use Dispatchable, SerializesModels;

    /**
     * Rate model.
     *
     * @var \App\Entities\Rates\Rate
     */
    public $rate;

    /**
     * Create a new event instance.
     *
     * @param  \App\Entities\Rates\Rate  $rate
     * @return void
     */
    public function __construct(Rate $rate)
    {
        $this->rate = $rate;
    }
}
