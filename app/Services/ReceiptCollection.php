<?php

namespace App\Services;

use App\Entities\Receipts\Receipt;
use Illuminate\Support\Collection;

/**
* Receipt Collection Class
*/
class ReceiptCollection
{
    private $instance;
    private $session;

    public function __construct()
    {
        $this->session = session();
        $this->instance('new_receipts');
    }

    public function instance($instance = null)
    {
        $instance = $instance ?: 'new_receipts';

        $this->instance = sprintf('%s.%s', 'receipts', $instance);

        return $this;
    }

    public function currentInstance()
    {
        return str_replace('receipts.', '', $this->instance);
    }

    public function addReceipt(Receipt $receipt)
    {
        $content = $this->getContent();
        $receipt->receiptKey = str_random(10);
        $content->put($receipt->receiptKey, $receipt);

        $this->session->put($this->instance, $content);

        return $receipt;
    }

    public function get($receiptKey)
    {
        $content = $this->getContent();
        if (isset($content[$receiptKey]))
            return $content[$receiptKey];

        return null;
    }

    public function updateReceiptData($receiptKey, $receiptAttributes)
    {
        $content = $this->getContent();

        foreach ($receiptAttributes as $attribute => $value) {
            $content[$receiptKey]->{$attribute} = $value;
        }

        $this->session->put($this->instance, $content);

        return $content[$receiptKey];
    }

    public function removeReceipt($receiptKey)
    {
        $content = $this->getContent();
        $content->pull($receiptKey);
        $this->session->put($this->instance, $content);
    }

    public function content()
    {
        if (is_null($this->session->get($this->instance))) {
            return collect([]);
        }

        return $this->session->get($this->instance);
    }

    protected function getContent()
    {
        $content = $this->session->has($this->instance) ? $this->session->get($this->instance) : collect([]);

        return $content;
    }

    public function keys()
    {
        return $this->getContent()->keys();
    }

    public function destroy()
    {
        $this->session->remove($this->instance);
    }

    public function addItemToReceipt($receiptKey, $item)
    {
        $content = $this->getContent();

        $item->setVolumetricDevider($content[$receiptKey]->volumetric_devider);

        $content[$receiptKey]->addItem($item);
        $content[$receiptKey] = $this->updateReceipt($content[$receiptKey]);

        $this->session->put($this->instance, $content);
    }

    public function updateReceipt(Receipt $receipt)
    {
        $receipt->charged_weight = $receipt->getChargedWeight();
        $receipt->items_count = $receipt->itemsCount();
        $receipt->pcs_count = $receipt->itemsCount();
        $receipt->base_charge = $receipt->getCharge();
        $receipt->subtotal = $receipt->getCharge();
        $receipt->total = $receipt->getCharge();
        return $receipt;
    }

    public function updateReceiptItem($receiptKey, $itemKey, $newItemData)
    {
        $content = $this->getContent();
        $content[$receiptKey]->updateItem($itemKey, $newItemData);
        $content[$receiptKey] = $this->updateReceipt($content[$receiptKey]);

        $this->session->put($this->instance, $content);
    }

    public function removeItemFromReceipt($receiptKey, $itemKey)
    {
        $content = $this->getContent();
        $content[$receiptKey]->removeItem($itemKey);

        $this->session->put($this->instance, $content);
    }

    public function count()
    {
        return $this->getContent()->count();
    }

    public function isEmpty()
    {
        return $this->count() == 0;
    }

    public function hasContent()
    {
        return !$this->isEmpty();
    }

}