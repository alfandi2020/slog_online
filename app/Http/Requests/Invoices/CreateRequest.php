<?php

namespace App\Http\Requests\Invoices;

use App\Http\Requests\Request;

class CreateRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
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
			'receipt_id' => 'required',
			'periode' 	 => 'required|unique:invoices,periode,NULL,id,customer_id,'
							. $this->get('customer_id'),
			'discount'   => 'nullable|numeric',
			'admin_fee'  => 'nullable|numeric',
			'notes'  	 => 'nullable|max:255',
		];
	}

	public function messages()
	{
	    return [
			'receipt_id.required' => 'Resi harus dipilih.'
	    ];
	}

}
