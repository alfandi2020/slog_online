<?php

namespace App\Events\Manifests;

use App\Entities\Manifests\Manifest;

class HandoverReceived
{
    public $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }
}
