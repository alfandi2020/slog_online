<?php

namespace App\Http\Requests\Receipts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit', $this->route('receipt'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Receipt Data
            'customer_id'           => 'nullable|exists:customers,id',
            'payment_type_id'       => 'required|numeric',
            'reference_no'          => 'nullable|max:30',
            'pickup_courier_id'     => 'nullable|numeric|exists:users,id,is_active,1',
            'customer_invoice_no'   => 'nullable|string|max:255',
            'notes'                 => 'nullable|max:255',
            'pickup_time'           => 'required|date_format:Y-m-d H:i|before_or_equal:'.now(),
            'pack_type_id'          => 'required|numeric',
            'charged_weight'        => 'required|numeric|min:1',
            'items_count'           => 'required|numeric|min:1|max:9999',
            'pcs_count'             => 'required|numeric|min:1|max:9999',
            'pack_content'          => 'nullable|max:255',
            // Origin and Destination
            // 'number'                => 'required';
            // 'orig_city_id'          => 'required|numeric';
            // 'orig_district_id'      => 'required|numeric';
            // 'dest_city_id'          => 'required|numeric';
            // 'dest_district_id'      => 'required|numeric';
            // Consignor
            'consignor_name'        => 'required|string|max:60',
            'consignor_address.1'   => 'required|string|max:60',
            'consignor_address.2'   => 'nullable|string|max:60',
            'consignor_address.3'   => 'nullable|string|max:60',
            'consignor_postal_code' => 'nullable|string|max:10',
            'consignor_phone'       => 'required|string|max:60',
            // Consignee
            'consignee_name'        => 'required|string|max:60',
            'consignee_address.1'   => 'required|string|max:60',
            'consignee_address.2'   => 'nullable|string|max:60',
            'consignee_address.3'   => 'nullable|string|max:60',
            'consignee_postal_code' => 'nullable|string|max:10',
            'consignee_phone'       => 'required|string|max:60',
            // Costs
            'base_charge'           => 'required|numeric|min:0',
            'discount'              => 'required|numeric|min:0',
            'packing_cost'          => 'required|numeric|min:0',
            'insurance_cost'        => 'required|numeric|min:0',
            'add_cost'              => 'required|numeric|min:0',
            'admin_fee'             => 'required|numeric|min:0',
        ];
    }

    public function persist()
    {
        $receipt = $this->route('receipt');

        $receipt->customer_id = $this->get('customer_id');
        // $receipt->number           = $this->get('number');
        // $receipt->orig_city_id     = $this->get('orig_city_id');
        // $receipt->orig_district_id = $this->get('orig_district_id') ?: 0;
        // $receipt->dest_city_id     = $this->get('dest_city_id');
        // $receipt->dest_district_id = $this->get('dest_district_id') ?: 0;
        $receipt->payment_type_id = $this->get('payment_type_id');
        $receipt->reference_no = $this->get('reference_no');
        $receipt->pickup_courier_id = $this->get('pickup_courier_id');
        $receipt->customer_invoice_no = cleanUpCustomerInvoiceNo($this->get('customer_invoice_no'));
        $receipt->notes = $this->get('notes');
        $receipt->pickup_time = $this->get('pickup_time').':00';
        $receipt->pack_type_id = $this->get('pack_type_id');
        $receipt->weight = $this->get('charged_weight');
        $receipt->items_count = $this->get('items_count');
        $receipt->pcs_count = $this->get('pcs_count');
        $receipt->pack_content = $this->get('pack_content');
        $receipt->costs_detail = $this->getCostDetail();
        $receipt->amount = $this->getCostDetail()['total'];
        $receipt->bill_amount = $this->getCostDetail()['total'];
        $receipt->consignor = [
            'name'        => $this->get('consignor_name'),
            'address'     => [
                1 => $this->get('consignor_address')[1],
                2 => $this->get('consignor_address')[2],
                3 => $this->get('consignor_address')[3],
            ],
            'postal_code' => $this->get('consignor_postal_code'),
            'phone'       => $this->get('consignor_phone'),
        ];
        $receipt->consignee = [
            'name'        => $this->get('consignee_name'),
            'address'     => [
                1 => $this->get('consignee_address')[1],
                2 => $this->get('consignee_address')[2],
                3 => $this->get('consignee_address')[3],
            ],
            'postal_code' => $this->get('consignee_postal_code'),
            'phone'       => $this->get('consignee_phone'),
        ];
        $receipt->save();

        return $receipt;
    }

    private function getCostDetail()
    {
        $subtotal = $this->get('base_charge') - $this->get('discount');
        $total = $subtotal
         + $this->get('packing_cost')
         + $this->get('insurance_cost')
         + $this->get('add_cost')
         + $this->get('admin_fee');

        return [
            'base_charge'    => (int) $this->get('base_charge'),
            'discount'       => (int) $this->get('discount'),
            'subtotal'       => (int) $subtotal,
            'packing_cost'   => (int) $this->get('packing_cost'),
            'insurance_cost' => (int) $this->get('insurance_cost'),
            'add_cost'       => (int) $this->get('add_cost'),
            'admin_fee'      => (int) $this->get('admin_fee'),
            'total'          => (int) $total,
        ];
    }

    public function messages()
    {
        return [
            'pickup_time.before_or_equal' => 'Isi waktu dengan benar.',
        ];
    }
}
