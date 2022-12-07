<?php

namespace App\Entities\Invoices;

use Laracasts\Presenter\Presenter;

class InvoicePresenter extends Presenter
{
    public function status()
    {
        $status = [
            'name'  => 'On Proccess',
            'code'  => 'on_proccess',
            'class' => 'default',
        ];

        if (!is_null($this->sent_date)) {
            $status = [
                'name'  => 'Sent',
                'code'  => 'sent',
                'class' => 'info',
            ];
        }

        if (!is_null($this->payment_date)) {
            $status = [
                'name'  => 'Paid',
                'code'  => 'paid',
                'class' => 'warning',
            ];
        }

        if (!is_null($this->verify_date)) {
            $status = [
                'name'  => 'Closed',
                'code'  => 'closed',
                'class' => 'success',
            ];
        }

        if (!is_null($this->problem_date)) {
            $status = [
                'name'  => 'Macet',
                'code'  => 'problem',
                'class' => 'danger',
            ];
        }

        return $status;
    }

    public function statusLabel()
    {
        $string = '<span class="label label-';
        $string .= $this->status['class'];
        $string .= '">';
        $string .= $this->status['name'];
        $string .= '</span>';

        return $string;
    }

    public function numberLink($overrides = [])
    {
        $linkOptions = array_merge([
            'title' => 'Lihat detail Invoice '.$this->number,
        ], $overrides);
        return link_to_route('invoices.show', $this->number, [$this->id], $linkOptions);
    }
}
