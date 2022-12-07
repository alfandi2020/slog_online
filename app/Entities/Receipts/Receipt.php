<?php

namespace App\Entities\Receipts;

use Carbon\Carbon;
use App\Entities\Services\Rate;
use App\Entities\Receipts\Proof;
use App\Entities\Services\Service;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Entities\Receipts\Traits\Relations;
use App\Entities\Receipts\Traits\ReceiptItem;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Entities\Receipts\Traits\OriginDestination;
use App\Entities\Invoices\Invoice;

class Receipt extends Model
{
    use SoftDeletes, OriginDestination, PresentableTrait, Relations, ReceiptItem;

    protected $presenter = ReceiptPresenter::class;
    public $receiptKey;
    private $items = [];
    protected $dates = ['deleted_at', 'pickup_time'];

    public $casts = [
        'items'        => 'array',
        'consignor'    => 'array',
        'consignee'    => 'array',
        'costs_detail' => 'array',
        'items_detail' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'number';
    }

    public function getVolumetricDeviderAttribute()
    {
        return $this->service_id == 11 ? 6000 : 4000;
    }

    public function getChargedOnLabelAttribute()
    {
        $chargedOnOptions = [1 => trans('receipt.charged_on_weight'), trans('receipt.charged_on_item')];
        return $chargedOnOptions[$this->charged_on];
    }

    public function service()
    {
        return strtoupper(Service::getById($this->service_id));
    }

    public function hasStatusOf(array $statusCodes)
    {
        return in_array($this->status_code, $statusCodes);
    }

    public function isDelivered()
    {
        return $this->hasStatusOf(['dl', 'bd', 'or', 'rt', 'ma', 'ir']);
    }

    public function isInvoiceReady()
    {
        // return $this->hasStatusOf(['ir']) && $this->payment_type == 2;
        // @TODO Only credit receipt that can be invoiced
        return $this->hasStatusOf(['ir']);
    }

    public function isDraft()
    {
        return !!$this->receiptKey;
    }

    public function isInsured()
    {
        return !!$this->add_costs['insurance_cost'] ? trans('app.yes') : trans('app.no');
    }

    public function getBaseCost()
    {
        return $this->amount - $this->add_costs['packing_cost'] - $this->add_costs['insurance_cost'] + $this->add_costs['discount'];
    }

    public function getSubtotalCost()
    {
        return $this->amount - $this->add_costs['packing_cost'] - $this->add_costs['insurance_cost'];
    }

    public function getCharge()
    {
        if (is_null($this->rate)) {
            return 0;
        }

        if ($this->charged_on == 1) {
            return $this->rate->rate_kg * $this->getChargedWeight();
        }

        return $this->rate->rate_pc * $this->getChargedWeight();
    }

    public function progressList()
    {
        $progressList = [];
        $progressList[] = [
            'stop'        => $this->originName(),
            'time'        => $this->pickup_time->format('d-m-Y H:i'),
            'status'      => trans('receipt_status.de'),
            'notes'       => null,
            'handler'     => $this->creator->name,
            'status_code' => 'de',
        ];

        foreach ($this->progress as $key => $progressItem) {
            if ($progressItem->manifest->type_id == 3) {
                $distManifest = $progressItem->manifest;
                $progressList[] = [
                    'stop'        => $progressItem->origin->name,
                    'time'        => $distManifest->created_at->format('d-m-Y H:i'),
                    'status'      => trans('receipt_status.mn'),
                    'notes'       => $distManifest->notes,
                    'handler'     => $distManifest->creator->name,
                    'status_code' => 'mn',
                ];
            }

            $statusString = trans('receipt_status.'.$progressItem->start_status);

            // If delivery manifest
            if ($progressItem->manifest->type_id == 2) {
                $statusString .= ' to '.$progressItem->manifest->destinationNetwork->origin->name;
            }

            $progressList[] = [
                'id'          => $progressItem->id,
                'stop'        => $progressItem->origin->name,
                'time'        => $progressItem->created_at->format('d-m-Y H:i'),
                'status'      => $statusString,
                'notes'       => $progressItem->notes,
                'handler'     => $progressItem->creator->name,
                'status_code' => $progressItem->start_status,
            ];
            if ($proof = $progressItem->proof) {
                $progressList[] = [
                    'stop'        => $proof->location->name,
                    'time'        => $proof->delivered_at->format('d-m-Y H:i'),
                    'status'      => trans('receipt_status.'.$proof->status_code),
                    'notes'       => $proof->notes,
                    'handler'     => $proof->creator->name,
                    'status_code' => $proof->status_code,
                ];

                if (in_array($progressItem->end_status, ['dl', 'bd'])) {
                    continue;
                }
            }
            if ($progressItem->handler_id) {
                $statusString = trans('receipt_status.'.$progressItem->end_status);
                // If delivery manifest
                if ($progressItem->manifest->type_id == 2) {
                    $statusString = 'Received on '.$progressItem->manifest->destinationNetwork->origin->name;
                }

                $progressList[] = [
                    'stop'        => $progressItem->destination->name,
                    'time'        => $progressItem->updated_at->format('d-m-Y H:i'),
                    'status'      => $statusString,
                    'notes'       => $progressItem->notes,
                    'handler'     => $progressItem->handler->name,
                    'status_code' => $progressItem->end_status,
                ];
            }
        }

        if (!is_null($this->invoice_id)) {
            $invoice = $this->invoice;

            $progressList[] = [
                'stop'        => $invoice->network->cityOrigin->name,
                'time'        => $invoice->created_at->format('d-m-Y H:i'),
                'status'      => trans('receipt_status.id').' ('.$invoice->present()->numberLink(['target' => '_blank']).')',
                'notes'       => $invoice->notes,
                'handler'     => $invoice->creator->name,
                'status_code' => 'id',
            ];

            if ($invoice->isVerified()) {
                $progressList[] = [
                    'stop'        => $invoice->network->cityOrigin->name,
                    'time'        => Carbon::parse($invoice->verify_date)->format('d-m-Y'),
                    'status'      => trans('receipt.payment_statuses.closed').' ('.$invoice->present()->numberLink(['target' => '_blank']).')',
                    'notes'       => $invoice->notes,
                    'handler'     => $invoice->handler->name,
                    'status_code' => 'closed',
                ];
            }
        }
        return $progressList;
    }

    public function setStatusCode(string $statusCode)
    {
        $this->forceFill([
            'status_code'      => $statusCode,
            'last_officer_id'  => auth()->id(),
            'last_location_id' => auth()->check() ? auth()->user()->network->origin->id : $this->orig_city_id,
        ])->save();
        return $this;
    }

    public static function findByNumber($number)
    {
        return static::where('number', $number)->first();
    }

    public function isOnManifestProgress()
    {
        return !$this->progressIsAllClosed();
    }

    public function progressIsAllClosed()
    {
        $progresses = $this->progress;
        // if ($progresses->isEmpty())
        //     return true;

        return $progresses->isEmpty() || $progresses->filter(function ($progress) {
            return $progress->isOpen();
        })->isEmpty();
    }

    public function getTypeAttribute()
    {
        $type = 'Retail';

        switch ($this->service_id) {
            case 21:$type = 'Darat dan Laut';
                break;
            case 31:$type = 'Internasional';
                break;
            case 41:$type = 'Borongan';
                break;
            case 42:$type = 'Carter';
                break;
        }

        return $type;
    }

    public function getPaymentTypeAttribute()
    {
        return PaymentType::getNameById($this->payment_type_id);
    }

    public function lastLocation()
    {
        if (!is_null($this->lastProgress)) {
            return $this->lastProgress->destination
            ? $this->lastProgress->destination->name
            : $this->lastProgress->origin->name;
        }
        return $this->originName();
    }

    public function lastOfficer()
    {
        if (!is_null($this->lastProgress)) {
            return $this->lastProgress->handler
            ? $this->lastProgress->handler->name
            : $this->lastProgress->creator->name;
        }
        return $this->creator->name;
    }

    public function lastOfficerId()
    {
        if (!is_null($this->lastProgress)) {
            return $this->lastProgress->handler_id
            ? $this->lastProgress->handler_id
            : $this->lastProgress->creator_id;
        }
        return $this->creator_id;
    }

    public function lastStatus()
    {
        if (!is_null($this->lastProgress)) {
            return $this->lastProgress->end_status
            ? Status::getNameById($this->lastProgress->end_status)
            : Status::getNameById($this->lastProgress->start_status);
        }
        return Status::getNameById($this->status_code);
    }

    public function path()
    {
        return route('receipts.show', $this->number);
    }

    public function pod()
    {
        return $this->hasOne(Progress::class)->whereIn('end_status', ['dl', 'bd']);
    }

    public function proof()
    {
        return $this->hasOne(Proof::class);
    }

    public function numberLink()
    {
        return link_to_route('receipts.show', $this->number, [$this->number], [
            'title'  => 'Lihat detail Resi '.$this->number,
            'target' => '_blank',
        ]);
    }

    public function paymentIsClosed()
    {
        if ($this->invoice_id && $this->invoice->isVerified()) {
            return true;
        }

        return false;
    }

    public function hasPaymentType(array $paymentTypes)
    {
        $availablePaymentType = PaymentType::all()->all();
        return in_array($availablePaymentType[$this->payment_type_id], $paymentTypes);
    }

    public function recalculateBillAmount()
    {
        $rate = $this->getCorrectCustomerRate();
        $this->rate_id = $rate->id;

        // 1: weight, 2: pc
        if ($this->charged_on == 2) {
            $baseCharge = $this->pcs_count * $rate->rate_pc;
            $costsDetail = $this->recalculateCostDetail($rate, $baseCharge);

            $this->amount = $costsDetail['total'];
            $this->base_rate = $rate->rate_pc;
            $this->bill_amount = $costsDetail['total'];
            $this->costs_detail = $costsDetail;
        } else {
            if ($rate->min_weight <= $this->weight) {
                $baseCharge = $this->weight * $rate->rate_kg;
            } else {
                $baseCharge = $rate->min_weight * $rate->rate_kg;
            }
            $costsDetail = $this->recalculateCostDetail($rate, $baseCharge);

            $this->amount = $costsDetail['total'];
            $this->base_rate = $rate->rate_kg;
            $this->bill_amount = $costsDetail['total'];
            $this->costs_detail = $costsDetail;
        }

        $this->save();
    }

    public function recalculateCostDetail(Rate $rate, $baseCharge)
    {
        $costsDetail = $this->costs_detail;

        $subtotal = $baseCharge - $costsDetail['discount'];
        $total = $subtotal
             + $costsDetail['packing_cost']
             + $costsDetail['insurance_cost']
             + $costsDetail['add_cost']
             + $costsDetail['admin_fee'];

        return [
            'base_charge'    => (int) $baseCharge,
            'discount'       => (int) $costsDetail['discount'],
            'subtotal'       => (int) $subtotal,
            'packing_cost'   => (int) $costsDetail['packing_cost'],
            'insurance_cost' => (int) $costsDetail['insurance_cost'],
            'add_cost'       => (int) $costsDetail['add_cost'],
            'admin_fee'      => (int) $costsDetail['admin_fee'],
            'total'          => (int) $total,
        ];
    }

    public function getCorrectCustomerRate()
    {
        $rate = $this->rate;

        if (!is_null($this->customer_id)) {
            $customerRate = Rate::where([
                'customer_id'      => $this->customer_id,
                'service_id'       => $this->service_id,
                // 'pack_type_id' => $this->pack_type_id,
                'orig_city_id'     => $this->orig_city_id,
                'orig_district_id' => $this->orig_district_id,
                'dest_city_id'     => $this->dest_city_id,
                'dest_district_id' => $this->dest_district_id,
            ])->first();

            if ($customerRate && $this->rate_id !== $customerRate->id) {
                $rate = $customerRate;
            }
        }

        return $rate;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
