<?php

namespace App\Entities\Manifests;

use App\Entities\BaseRepository;
use App\Entities\Receipts\Receipt;
use DB;

/**
* Distributions Repository Class
*/
class DistributionsRepository extends ManifestsRepository
{
    public function getLatest($type = null)
    {
        $manifests = $this->model->latest()
            ->with('originNetwork','destinationNetwork')
            ->withCount('receipts')
            ->where(function($q) {
                $q->where('orig_network_id', auth()->user()->network_id);
                $q->where('type_id', 3);
            })
            ->with('destinationCity', 'receipts', 'creator', 'handler')
            ->paginate($this->_paginate);
        return $manifests;
    }

    public function update($manifestData, $manifestId)
    {
        $manifest = $this->requireById($manifestId);
        $manifest->dest_city_id     = $manifestData['dest_city_id'];
        $manifest->handler_id       = $manifestData['handler_id'];
        $manifest->delivery_unit_id = $manifestData['delivery_unit_id'];

        if ($manifestData['deliver_at'])
            $manifest->deliver_at   = $manifestData['deliver_at'] . ':00';

        if (isset($manifestData['received_at']) && $manifestData['received_at'])
            $manifest->received_at  = $manifestData['received_at'] . ':00';

        $manifest->start_km         = $manifestData['start_km'];
        $manifest->end_km           = $manifestData['end_km'] ?? null;
        $manifest->notes            = $manifestData['notes'];
        $manifest->save();

        return $manifest;
    }

    public function sendManifestWithData($manifestId, $manifestData = [])
    {
        $manifest = $this->requireById($manifestId);

        if ($manifest->isSent() || $manifest->receipts()->count() == 0)
            return false;

        DB::beginTransaction();
        $manifest->receipts->each->setStatusCode('od');
        $manifest->deliver_at = $manifestData['deliver_at'] . ':00';
        $manifest->start_km   = $manifestData['start_km'];
        $manifest->notes      = $manifestData['notes'];
        $manifest->save();
        DB::commit();

        return $manifest;
    }
}