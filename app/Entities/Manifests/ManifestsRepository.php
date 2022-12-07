<?php

namespace App\Entities\Manifests;

use App\Entities\BaseRepository;
use App\Entities\Networks\DeliveryUnit;
use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;

/**
 * Manifests Repository Class
 */
class ManifestsRepository extends BaseRepository
{
    protected $model;
    protected $_paginate = 25;

    public function __construct(Manifest $model)
    {
        parent::__construct($model);
    }

    public function searchByNumber($manifestNumber)
    {
        if (is_null($manifestNumber)) {
            return [];
        }

        $manifests = $this->model->latest()
            ->with('originNetwork', 'destinationNetwork')
            ->where('number', 'like', '%'.$manifestNumber.'%')
            ->paginate($this->_paginate);
        return $manifests;
    }

    public function assignReceipt($manifestId, $receiptNumber)
    {
        $manifest = $this->requireById($manifestId);
        $receipt = Receipt::where('number', $receiptNumber)->firstOrFail();
        $result = $manifest->addReceipt($receipt);

        return $result;
    }

    public function removeReceipt($manifestId, $receiptNumber)
    {
        $manifest = $this->requireById($manifestId);
        $receipt = Receipt::where('number', $receiptNumber)->firstOrFail();
        $result = $manifest->removeReceipt($receipt);

        return $result;
    }

    public function sendManifest($manifestId)
    {
        return $this->requireById($manifestId)->send();
    }

    public function takeManifestBack($manifestId)
    {
        return $this->requireById($manifestId)->takeBack();
    }

    public function checkReceipt($manifestId, $receiptNumber)
    {
        $manifest = $this->requireById($manifestId);
        return $manifest->checkReceipt($receiptNumber);
    }

    public function rejectReceipt($manifestId, $receiptNumber)
    {
        $manifest = $this->requireById($manifestId);
        return $manifest->rejectReceipt($receiptNumber);
    }

    public function receiveManifest($manifestId)
    {
        $manifest = $this->requireById($manifestId);

        return $manifest->receive();
    }

    public function getCouriersList()
    {
        return User::where([
            'network_id' => auth()->user()->network_id,
            'role_id'    => 7,
            'is_active'  => 1,
        ])->pluck('name', 'id');
    }

    public function getDeliveryUnitsList()
    {
        $unitsList = [];
        $units = DeliveryUnit::where([
            'network_id' => auth()->user()->network_id,
        ])->get();
        foreach ($units as $key => $unit) {
            $unitsList[$unit->id] = $unit->plat_no.' - '.$unit->name;
        }

        return $unitsList;
    }
}
