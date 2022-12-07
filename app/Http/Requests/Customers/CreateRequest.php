<?php

namespace App\Http\Requests\Customers;

use App\Entities\Networks\Network;
use App\Entities\Customers\Customer;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', new Customer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comodity_id' => 'required|exists:site_references,id',
            'network_id'  => 'required|exists:networks,id',
            'code'        => 'nullable|unique:customers,code',
            'name'        => 'required|string|max:60',
            'pic.name'    => 'required|string|max:60',
            'pic.phone'   => 'required|string|max:20',
            'pic.email'   => 'required|email|max:60',
            'address.1'   => 'required|string|max:100',
            'address.2'   => 'nullable|string|max:100',
            'address.3'   => 'nullable|string|max:50',
            'start_date'  => 'required|date_format:Y-m-d',
            'is_taxed'    => 'required|boolean',
            'npwp'        => 'required_if:is_taxed,1',
            'category_id' => 'required|in:1,2,3',
        ];
    }

    public function messages()
    {
        return [
            'npwp.required_if' => trans('validation.required'),
        ];
    }

    public function persist()
    {
        $customer = new Customer;
        $customer->comodity_id = $this->get('comodity_id');
        $customer->network_id = $this->get('network_id');
        $customer->account_no = $this->getNewAccountNumber($this->get('network_id'));
        $customer->code = $this->get('code');
        $customer->name = $this->get('name');
        $customer->npwp = $this->get('npwp');
        $customer->is_taxed = $this->get('is_taxed');
        $customer->pic = $this->get('pic');
        $customer->start_date = $this->get('start_date');
        $customer->address = $this->get('address');
        $customer->category_id = $this->get('category_id');
        $customer->save();

        return $customer;
    }

    private function getNewAccountNumber($networkId)
    {
        $network = Network::findOrFail($networkId);
        $networkCode = $network->code;
        $lastNetworkCustomer = $network->customers->last();
        if (is_null($lastNetworkCustomer)) {
            return substr($network->code, 0, 4).'0001';
        }

        return ++$lastNetworkCustomer->account_no;
    }
}
