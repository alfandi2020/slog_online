<?php

namespace App\Entities\Manifests;

use App\Entities\BaseRepository;
use App\Entities\Receipts\Receipt;

/**
* Handovers Repository Class
*/
class HandoversRepository extends ManifestsRepository
{
    public function getLatest($type = null)
    {
        $manifests = $this->model->latest()
            ->with('creator','handler')
            ->withCount('receipts')
            ->where(function($q) use ($type) {
                if ($type == 'out')
                    $q->where('orig_network_id', auth()->user()->network_id);
                else
                    $q->where('dest_network_id', auth()->user()->network_id);

                $q->where('type_id', 1);
            })
            ->with('receipts', 'creator', 'handler')
            ->paginate($this->_paginate);
        return $manifests;
    }
}