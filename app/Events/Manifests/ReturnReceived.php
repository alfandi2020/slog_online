<?php

namespace App\Events\Manifests;

use App\Entities\Manifests\Manifest;

class ReturnReceived
{
    public $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }
}
