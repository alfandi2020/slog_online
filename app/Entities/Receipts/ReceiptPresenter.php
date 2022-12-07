<?php

namespace App\Entities\Receipts;

use App\Entities\Invoices\Invoice;
use Laracasts\Presenter\Presenter;

class ReceiptPresenter extends Presenter
{
    public function creatorName()
    {
        return $this->entity->creator->name;
    }

    public function customerName()
    {
        return $this->entity->customer_id ? $this->entity->customer->name : $this->entity->consignor['name'];
    }

    public function networkName()
    {
        return $this->entity->network->name;
    }

    //Get Creator Network
    public function creatorNetwork()
    {
        return $this->entity->creator->network->name;
    }

    public function barcode()
    {
        return \Html::image(url('barcode/img/' . $this->number . '/25'));
    }

    public function paymentType()
    {
        return PaymentType::getNameById($this->entity->payment_type_id);
    }

    public function paymentStatusLabel()
    {
        $paymentStatus = trans('receipt.payment_statuses.un_invoiced');
        $labelClass = 'default';

        if ($this->entity->invoice_id) {
            $labelClass = 'info';
            $paymentStatus = trans('receipt.payment_statuses.invoiced');
        }

        if ($this->entity->paymentIsClosed()) {
            $labelClass = 'success';
            $paymentStatus = trans('receipt.payment_statuses.closed');
        }

        $string = '<span class="label label-' . $labelClass . '">';
        $string .= $paymentStatus;
        $string .= '</span>';

        return $string;
    }

    public function chargedOn()
    {
        return $this->entity->charged_on == 1 ? trans('receipt.charged_on_weight') : trans('receipt.charged_on_item');
    }

    public function statusName()
    {
        return Status::getNameById($this->status_code);
    }

    public function statusLabel()
    {
        $statusName = Status::getNameById($this->status_code);
        if (!$this->status_code)
            $statusName = trans('receipt_status.de');

        $labelClass = in_array($this->status_code, ['dl', 'bd']) ? 'success' : 'default';

        if ($this->status_code == 'no') {
            $statusName = trans('receipt_status.no');
            $labelClass = 'danger';
        }

        // @TODO: need to check this has N+1 problem.
        if ($this->status_code == 'mn') {
            $this->entity->load('lastProgress.manifest.destinationNetwork.origin');
            $statusName = 'Manifested to ' . $this->entity->lastProgress->manifest->destinationNetwork->origin->name;
        }

        if ($this->status_code == 'rd') {
            $this->entity->load('lastProgress.manifest.destinationNetwork');
            $statusName = 'Received on ' . $this->entity->lastProgress->manifest->destinationNetwork->origin->name;
        }

        if ($this->status_code == 'ir' && !is_null($this->invoice_id)) {
            $statusName = trans('receipt_status.id');
            $labelClass = 'success';
        }

        $string = '<span class="label label-' . $labelClass . '">';
        $string .= $statusName;
        $string .= '</span>';

        return $string;
    }

    public function statusCodeLabel()
    {
        $statusCode = $this->status_code;
        $statusName = Status::getNameById($statusCode);
        if (!$statusCode)
            $statusName = trans('receipt_status.de');

        $labelClass = in_array($statusCode, ['dl', 'bd']) ? 'success' : 'default';

        if ($statusCode == 'ir' && !is_null($this->invoice_id)) {
            $statusName = trans('receipt_status.id');
            $statusCode = 'id';
            $labelClass = 'success';
        }

        $string = '<span class="label label-' . $labelClass . '" title="' . $statusName . '">';
        $string .= strtoupper($statusCode);
        $string .= '</span>';

        return $string;
    }
}
