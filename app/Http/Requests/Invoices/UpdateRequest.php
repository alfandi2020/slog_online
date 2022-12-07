<?php

namespace App\Http\Requests\Invoices;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit', $this->route('invoice'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date'       => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:date',
            'periode'    => 'required|unique:invoices,periode,'.$this->segment(3).',id,customer_id,'
            .$this->get('customer_id'),
            'discount'   => 'nullable|numeric',
            'admin_fee'  => 'nullable|numeric',
            'creator_id' => 'required|exists:users,id',
            'notes'      => 'nullable|max:255',
        ];
    }

}
