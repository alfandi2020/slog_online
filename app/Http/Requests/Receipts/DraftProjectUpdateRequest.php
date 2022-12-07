<?php

namespace App\Http\Requests\Receipts;

use App\Http\Requests\Request as FormRequest;
use App\Services\ReceiptCollection;

class DraftProjectUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array(auth()->user()->role_id, [1, 3, 4]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paymentTypeIds = '|in:1,2,3';

        if (is_null($this->get('customer_id'))) {
            $paymentTypeIds = '|in:1';
        }

        return [
            // Receipt Data
            'customer_id'         => 'nullable|numeric|exists:customers,id',
            'number'              => 'nullable|numeric|unique:receipts,number',
            'orig_city_id'        => 'required|numeric',
            'orig_district_id'    => 'nullable|numeric',
            'dest_city_id'        => 'required|numeric',
            'dest_district_id'    => 'nullable|numeric',
            'payment_type_id'     => 'required|numeric'.$paymentTypeIds,
            'reference_no'        => 'nullable|string|max:30',
            'pickup_courier_id'   => 'nullable|numeric|exists:users,id,is_active,1',
            'customer_invoice_no' => 'nullable|string|max:255',
            'notes'               => 'nullable|string',

            // Receipt Cost
            'pickup_time'    => 'required|date_format:Y-m-d H:i',
            'pack_type_id'   => 'required|numeric|exists:site_references,id,cat,pack_type',
            'charged_weight' => 'required|numeric',
            'pcs_count'      => 'required|numeric|min:1|max:9999',
            'items_count'    => 'required|numeric|min:1|max:9999',
            'pack_content'   => 'nullable|string',
            'base_charge'    => 'required|numeric',
            'discount'       => 'required|numeric',
            'packing_cost'   => 'required|numeric',
            'insurance_cost' => 'required|numeric',
            'admin_fee'      => 'required|numeric',

            // Receipt Consignor and Consignee
            'consignor_name'        => 'required|string|max:60',
            'consignor_address.1'   => 'required|string|max:60',
            'consignor_address.2'   => 'nullable|string|max:60',
            'consignor_address.3'   => 'nullable|string|max:60',
            'consignor_postal_code' => 'nullable|string|max:10',
            'consignor_phone'       => 'required|string|max:60',
            'consignee_name'        => 'required|string|max:60',
            'consignee_address.1'   => 'required|string|max:60',
            'consignee_address.2'   => 'nullable|string|max:60',
            'consignee_address.3'   => 'nullable|string|max:60',
            'consignee_postal_code' => 'nullable|string|max:10',
            'consignee_phone'       => 'required|string|max:60',
        ];
    }

    public function update($receiptKey)
    {
        $receiptData = [];
        // Receipt Data
        $receiptData['customer_id'] = $this->get('customer_id');
        $receiptData['number'] = $this->get('number');
        $receiptData['orig_city_id'] = $this->get('orig_city_id');
        $receiptData['orig_district_id'] = $this->get('orig_district_id');
        $receiptData['dest_city_id'] = $this->get('dest_city_id');
        $receiptData['dest_district_id'] = $this->get('dest_district_id');
        $receiptData['payment_type_id'] = $this->get('payment_type_id');
        $receiptData['reference_no'] = $this->get('reference_no');
        $receiptData['pickup_courier_id'] = $this->get('pickup_courier_id');
        $receiptData['customer_invoice_no'] = cleanUpCustomerInvoiceNo($this->get('customer_invoice_no'));
        $receiptData['notes'] = $this->get('notes');
        $receiptData['pickup_time'] = $this->get('pickup_time').':00';
        $receiptData['pack_type_id'] = $this->get('pack_type_id');
        $receiptData['charged_weight'] = $this->get('charged_weight');
        $receiptData['items_count'] = $this->get('items_count');
        $receiptData['pcs_count'] = $this->get('pcs_count');
        $receiptData['pack_content'] = $this->get('pack_content');

        // Receipt Cost
        $receiptData['base_rate'] = 0;
        $receiptData['base_charge'] = $this->get('base_charge');
        $receiptData['discount'] = $this->get('discount');
        $receiptData['subtotal'] = $this->getSubtotalCharge($this);
        $receiptData['insurance_cost'] = $this->get('insurance_cost');
        $receiptData['packing_cost'] = $this->get('packing_cost');
        $receiptData['add_cost'] = $this->get('add_cost');
        $receiptData['admin_fee'] = $this->get('admin_fee');
        $receiptData['total'] = $this->getTotalCharge($this);

        // Receipt Consignor and Consignee
        $receiptData['consignor'] = $this->getConsignorFormat($this);
        $receiptData['consignee'] = $this->getConsigneeFormat($this);

        $receipt = $this->receiptCollection->updateReceiptData($receiptKey, $receiptData);

        return $receipt;
    }

    public function messages()
    {
        return [
            'payment_type_id.in' => 'Jenis Pembayaran tidak Valid',
        ];
    }

    private $instance = 'new_receipts';

    public function __construct()
    {
        $this->receiptCollection = new ReceiptCollection;
        $this->receiptCollection->instance($this->instance);
    }

    private function getConsignorFormat($request)
    {
        $consignorData = [];
        $consignorData['name'] = $request->get('consignor_name');
        $consignorData['address'][1] = $request->get('consignor_address')[1];
        $consignorData['address'][2] = $request->get('consignor_address')[2];
        $consignorData['address'][3] = $request->get('consignor_address')[3];
        $consignorData['phone'] = $request->get('consignor_phone');
        $consignorData['postal_code'] = $request->get('consignor_postal_code');
        return $consignorData;
    }

    private function getConsigneeFormat($request)
    {
        $consigneeData = [];
        $consigneeData['name'] = $request->get('consignee_name');
        $consigneeData['address'][1] = $request->get('consignee_address')[1];
        $consigneeData['address'][2] = $request->get('consignee_address')[2];
        $consigneeData['address'][3] = $request->get('consignee_address')[3];
        $consigneeData['phone'] = $request->get('consignee_phone');
        $consigneeData['postal_code'] = $request->get('consignee_postal_code');
        return $consigneeData;
    }

    private function getSubtotalCharge()
    {
        return $this->base_charge - $this->discount;
    }

    private function getTotalCharge()
    {
        return $this->getSubtotalCharge() + $this->insurance_cost + $this->packing_cost + $this->add_cost + $this->admin_fee;
    }
}
