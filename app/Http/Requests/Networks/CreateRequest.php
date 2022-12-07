<?php

namespace App\Http\Requests\Networks;

use App\Entities\Networks\Network;
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
        return $this->user()->can('create', new Network);
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
            'origin_district_id' => 'required_if:type_id,3,4',
        ];
    }

    public function persist()
    {
        $data = $this->only(
            'type_id','name','address','coordinate','postal_code',
            'phone','email','origin_city_id','origin_district_id'
        );

        $data['code'] = $this->getNetworkCode($data['type_id'], $data['origin_city_id'], $data['origin_district_id']);

        return Network::create($data);
    }

    public function getNetworkCode($typeId, $origCityId, $origDistrictId)
    {
        switch ($typeId) {
            case 1: return substr($origCityId, 0, 2) . '000000'; break;
            case 2: return $origCityId . '0000'; break;
            case 3: return $origDistrictId . '0'; break;
            case 4:
                $lastOutlet = Network::where('type_id', 4)->where('origin_district_id', $origDistrictId)->first();
                if (is_null($lastOutlet))
                    return $origDistrictId . '1';

                return ++$lastOutlet->code;
                break;
        }
    }
}
