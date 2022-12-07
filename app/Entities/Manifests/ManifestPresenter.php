<?php

namespace App\Entities\Manifests;

use Laracasts\Presenter\Presenter;

class ManifestPresenter extends Presenter
{
    public function creatorName()
    {
        return $this->creator->name;
    }

    public function handlerName()
    {
        return $this->handler ? $this->handler->name : '-';
    }

    public function courierName()
    {
        return $this->courier ? $this->courier->name : null;
    }

    public function deliveryUnitName()
    {
        return $this->deliveryUnit ? $this->deliveryUnit->name : null;
    }

    public function customerName()
    {
        return $this->customer ? $this->customer->name : null;
    }

    public function customerLink()
    {
        return $this->customer ? link_to_route('customers.show', $this->customer->name, [$this->customer->id]) : null;
    }

    public function barcode()
    {
        return \Html::image(url('barcode/img/' . $this->number . '/25'));
    }

    public function pcsCount()
    {
        return $this->pcs_count ? $this->pcs_count . ' Koli' : $this->receipts->sum('items_count') . ' Koli';
    }

    public function weight()
    {
        if ($this->entity->weight)
            return displayWeight($this->entity->weight);

        $weight = $this->receipts->sum('weight');

        return displayWeight($weight);
    }

    public function typeLabel()
    {
        return '<span class="badge" style="background-color:' . $this->entity->typeColor() . '">' . $this->entity->type() . '</span>';
    }

    public function statusLabel()
    {
        return '<span class="label label-' . $this->status['class'] . '">' . $this->status['name'] . '</span>';
    }

    public function status()
    {
        $status = [
            'name' => 'On Proccess',
            'code' => 'on_proccess',
            'class' => 'default',
        ];

        if ($this->deliver_at) {
            $status = [
                'name' => 'On Delivery',
                'code' => 'on_proccess',
                'class' => 'info',
            ];
        }

        if ($this->received_at) {
            $status = [
                'name' => 'Received',
                'code' => 'received',
                'class' => 'success',
            ];
        }
        return $status;
    }

    public function numberLink($overrides = [])
    {
        $linkOptions = array_merge([
            'title' => 'Lihat detail Manifest ' . $this->number,
        ], $overrides);
        return link_to_route('manifests.' . str_plural($this->entity->typeCode()) . '.show', $this->number, [$this->number], $linkOptions);
    }
}