<?php

namespace App\Entities\Manifests;

/**
 * Deliveries Repository Class
 */
class DeliveriesRepository extends ManifestsRepository
{
    public function getLatest($type = null)
    {
        $manifests = $this->model->latest()
            ->where(function ($q) use ($type) {
                if ($type == 'out') {
                    $q->where('orig_network_id', auth()->user()->network_id);
                } else {
                    $q->where('dest_network_id', auth()->user()->network_id);
                }

                $q->where('type_id', 2);
            })
            ->with('originNetwork', 'destinationNetwork', 'receipts', 'creator', 'handler', 'deliveryCourier')
            ->withCount('receipts')
            ->paginate($this->_paginate);
        return $manifests;
    }
}
