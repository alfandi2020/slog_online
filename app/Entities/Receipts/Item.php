<?php

namespace App\Entities\Receipts;

use App\Entities\References\Reference;

/**
* Receipts\Item
*/
class Item
{
    public $weight;
    public $length;
    public $width;
    public $height;
    public $volumetricDevider = 6000;
    public $type_id;
    public $notes;

    public function __construct($weight = 1, $length = null, $width = null, $height = null, $type_id = null, $notes = null)
    {
        $this->weight = $weight;
        $this->length = $length;
        $this->width  = $width;
        $this->height = $height;
        $this->type_id = $type_id;
        $this->notes  = $notes;
    }

    public function getVolume()
    {
        if (is_null($this->length) || is_null($this->width) || is_null($this->height))
            return null;

        return $this->length * $this->width * $this->height;
    }

    public function getVolumetricWeight($serviceType = 'retail')
    {
        if (is_null($this->getVolume()))
            return null;

        if ($serviceType == 'sal')
            $this->setVolumetricDevider(4000);

        return $this->getVolume() / $this->volumetricDevider;
    }

    public function getChargedWeight()
    {
        if (is_null($this->getVolumetricWeight())
            || $this->getVolumetricWeight() < $this->weight)
            return $this->weight;

        return $this->getVolumetricWeight();
    }

    public function setVolumetricDevider($volumetricDevider)
    {
        $this->volumetricDevider = $volumetricDevider;
    }

    public function toArray()
    {
        $itemsArray = get_object_vars($this);
        $itemsArray['volume'] = $this->getVolume();
        $itemsArray['volumetric_weight'] = $this->getVolumetricWeight();
        $itemsArray['charged_weight'] = $this->getChargedWeight();
        $itemsArray['type'] = $this->getType();

        return $itemsArray;
    }

    public function getType()
    {
        if (is_null($this->type_id))
            return 'Dokumen';

        return $this->getTypeModel()->name;
    }

    private function getTypeModel()
    {
        return Reference::findOrFail($this->type_id);
    }
}