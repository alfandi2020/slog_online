<?php

namespace App\Http\Requests\Networks;

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
        return $this->user()->can('update', $this->route('network'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type_id'            => 'required|numeric|in:1,2,3,4',
            'name'               => 'required|max:60',
            'address'            => 'nullable|max:100',
            'coordinate'         => 'nullable|max:60',
            'postal_code'        => 'nullable|max:10',
            'phone'              => 'nullable|max:20',
            'email'              => 'nullable|email|max:60',
            'province_id'        => 'required|numeric',
            'origin_city_id'     => 'required|numeric',
            'origin_district_id' => 'nullable|required_if:type_id,3,4|numeric',
        ];
    }

    public function messages()
    {
        return [
            'origin_district_id.required_if' => 'Wajib diisi.',
        ];
    }

    public function persist()
    {
        $data = $this->only(
            'type_id','name','address','coordinate','postal_code',
            'phone','email','origin_city_id','origin_district_id'
        );

        if (in_array($data['type_id'], [1,2])) $data['origin_district_id'] = null;

        $network = $this->route('network');
        $network->update($data);
        return $network;
    }
}
