<?php

namespace App\Http\Controllers\Receipts;

use App\Entities\Receipts\Receipt;
use App\Http\Requests\Receipts\DraftProjectUpdateRequest;
use Illuminate\Http\Request;

class DraftProjectsController extends DraftReceiptsController
{
    public function add(Request $request)
    {
        $receipt = new Receipt;
        $receipt->rate_id = $request->get('rate_id');
        $receipt->charged_weight = $request->get('charged_weight');
        $receipt->service_id = $request->get('service_id');
        $receipt->orig_city_id = $request->get('orig_city_id', $request->user()->network->origin_city_id);
        $receipt->orig_district_id = $request->get('orig_district_id', $request->user()->network->origin_district_id);
        $receipt->dest_city_id = $request->get('dest_city_id');
        $receipt->dest_district_id = $request->get('dest_district_id');

        $receipt = $this->receiptCollection->addReceipt($receipt);

        return redirect()->route('receipts.draft', $receipt->receiptKey);
    }

    public function update(DraftProjectUpdateRequest $receiptForm, $receiptKey)
    {
        $receipt = $receiptForm->update($receiptKey);

        flash(trans('receipt.draft_updated'), 'success');

        return redirect()->route('receipts.draft', [$receiptKey, 'step' => 3]);
    }

    public function store(Request $request, $receiptKey)
    {
        $receipt = $this->receiptCollection->get($receiptKey);
        $user = auth()->user();
        $newReceipt = new Receipt;
        $newReceipt->service_id = $receipt->service_id;
        $newReceipt->number = $this->getNewReceiptNumber($receipt->number);
        $newReceipt->pickup_time = $receipt->pickup_time;
        $newReceipt->items_detail = $receipt->itemsArray() ?: null;
        $newReceipt->pcs_count = $receipt->pcs_count;
        $newReceipt->items_count = $receipt->items_count;
        $newReceipt->weight = $receipt->charged_weight;
        $newReceipt->pack_content = $receipt->pack_content ?: null;
        $newReceipt->pack_value = $receipt->pack_value;
        $newReceipt->orig_city_id = $receipt->orig_city_id;
        $newReceipt->orig_district_id = $receipt->orig_district_id ?: 0;
        $newReceipt->dest_city_id = $receipt->dest_city_id;
        $newReceipt->dest_district_id = $receipt->dest_district_id ?: 0;
        $newReceipt->charged_on = 1;
        $newReceipt->consignor = $receipt->consignor;
        $newReceipt->consignee = $receipt->consignee;
        $newReceipt->creator_id = $user->id;
        // $newReceipt->network_id = $user->network_id;
        $newReceipt->network_id = $receipt->customer->network_id; //Refer to Customer Registered Network
        $newReceipt->status_code = 'de';
        $newReceipt->invoice_id = null;
        $newReceipt->rate_id = $receipt->rate_id;
        $newReceipt->amount = $receipt->total;
        $newReceipt->bill_amount = $receipt->total;
        $newReceipt->base_rate = $receipt->base_rate;
        $newReceipt->reference_no = $receipt->reference_no ?: null;
        $newReceipt->pickup_courier_id = $receipt->pickup_courier_id ?: null;
        $newReceipt->customer_invoice_no = $receipt->customer_invoice_no ?: null;
        $newReceipt->payment_type_id = $receipt->payment_type_id;
        // $newReceipt->customer_id = $receipt->customer_id ?: null;
        $newReceipt->customer_id = $receipt->customer_id; //All Transaction need to customer_id to procced
        $newReceipt->pack_type_id = $receipt->pack_type_id;
        $newReceipt->costs_detail = [
            'base_charge'    => (int) $receipt->base_charge,
            'discount'       => (int) $receipt->discount,
            'subtotal'       => (int) $receipt->subtotal,
            'insurance_cost' => (int) $receipt->insurance_cost,
            'packing_cost'   => (int) $receipt->packing_cost,
            'add_cost'       => 0,
            'admin_fee'      => (int) $receipt->admin_fee,
            'total'          => (int) $receipt->total,
        ];
        $newReceipt->notes = $receipt->notes ?: null;
        $newReceipt->deleted_at = null;
        $newReceipt->image_proof = 'noProofImage.jpg';

        $newReceipt->save();

        $this->receiptCollection->removeReceipt($receiptKey);

        flash(trans('receipt.created'), 'success');
        return redirect()->route('receipts.show', $newReceipt->number);
    }
}
