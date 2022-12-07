<?php

namespace App\Http\Requests\Receipts;

use App\Services\ReceiptCollection;
use App\Http\Requests\Request as FormRequest;

class DraftUpdateRequest extends FormRequest
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
        return [
            'number'                => 'nullable|numeric|unique:receipts,number',
            'reference_no'          => 'nullable|string|max:30',
            'pickup_courier_id'     => 'nullable|numeric|exists:users,id,is_active,1',
            'customer_invoice_no'   => 'nullable|string|max:255',
            'pickup_time'           => 'required|date_format:Y-m-d H:i|before_or_equal:'.now(),
            'payment_type_id'       => 'required|numeric',
            'pack_content'          => 'nullable|string',
            'notes'                 => 'nullable|string',
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
        $receiptData['number'] = $this->get('number');
        $receiptData['reference_no'] = $this->get('reference_no');
        $receiptData['pickup_courier_id'] = $this->get('pickup_courier_id');
        $receiptData['customer_invoice_no'] = cleanUpCustomerInvoiceNo($this->get('customer_invoice_no'));
        $receiptData['pickup_time'] = $this->get('pickup_time').':00';
        $receiptData['payment_type_id'] = $this->get('payment_type_id');
        $receiptData['pack_content'] = $this->get('pack_content');
        $receiptData['notes'] = $this->get('notes');
        $receiptData['consignor'] = $this->getConsignorFormat($this);
        $receiptData['consignee'] = $this->getConsigneeFormat($this);

        $receipt = $this->receiptCollection->updateReceiptData($receiptKey, $receiptData);

        return $receipt;
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

    public function messages()
    {
        return [
            'pickup_time.before_or_equal' => 'Isi waktu dengan benar.',
        ];
    }
}
