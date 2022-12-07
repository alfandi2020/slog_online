<?php

namespace App\Events\Manifests;

use App\Entities\Manifests\Manifest;

class HandoverSent
{
    public $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }
}
