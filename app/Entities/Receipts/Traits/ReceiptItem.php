<?php

namespace App\Entities\Receipts\Traits;

use App\Entities\Receipts\Item;

trait ReceiptItem {

    public function firstItem()
    {
        return $this->items()->first();
    }

    public function lastItem()
    {
        return $this->items()->last();
    }

    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    public function updateItem($key, $newItemData)
    {
        if (!isset($this->items[$key]))
            return null;

        $item = $this->items[$key];
        foreach ($newItemData as $attribute => $value) {
            if ($value == '')
                $item->{$attribute} = null;
            else
                $item->{$attribute} = $value;
        }

        $this->items[$key] = $item;

        return $item;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
    }

    public function items()
    {
        return collect($this->items);
    }

    public function itemsArray()
    {
        $items = [];

        foreach ($this->items as $key => $item) {
            $items[$key] = $item->toArray();
        }

        return $items;
    }

    public function itemsCount()
    {
        return count($this->items);
    }

    public function getChargedWeightSum()
    {
        return $this->items()->sum(function($item) {
            return $item->getChargedWeight();
        });
    }

    public function getChargedWeight()
    {
        $chargedWeight = $this->getChargedWeightSum();

        return ceil($chargedWeight);
    }

}