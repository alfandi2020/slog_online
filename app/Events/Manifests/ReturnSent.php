<?php

namespace App\Events\Manifests;

use App\Entities\Manifests\Manifest;

class ReturnSent
{
    public $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }
}
