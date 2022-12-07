<?php

namespace App\Entities\Manifests;

use App\Entities\BaseRepository;
use App\Entities\Receipts\Receipt;

/**
* Accounting Manifests Repository Class
*/
class AccountingsRepository extends ManifestsRepository
{
    public function getLatestManifests()
    {
        $manifests = $this->model->latest()
            ->with('creator','handler')
            ->withCount('receipts')
            ->where(function($q) {
                $q->where('orig_network_id', auth()->user()->network_id);
                $q->where('type_id', 5);
            })
            ->with('receipts', 'customer', 'creator', 'handler')
            ->paginate($this->_paginate);
        return $manifests;
    }
}