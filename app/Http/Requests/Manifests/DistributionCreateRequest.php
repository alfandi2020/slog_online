<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;

class DistributionCreateRequest extends ManifestCreateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', new Manifest);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dest_city_id'     => 'required|exists:cities,id',
            'handler_id'       => 'required|numeric|exists:users,id',
            'delivery_unit_id' => 'nullable|numeric|exists:delivery_units,id',
            'notes'            => 'nullable|string|max:255',
        ];
    }

    public function persist()
    {
        $user = auth()->user();
        $manifest = new Manifest;
        $manifest->number           = $this->getNewManifestNumber('M3');
        $manifest->orig_network_id  = $user->network_id;
        $manifest->dest_network_id  = $user->network_id;
        $manifest->type_id          = 3;
        $manifest->dest_city_id     = $this->get('dest_city_id');
        $manifest->handler_id       = $this->get('handler_id');
        $manifest->delivery_unit_id = $this->get('delivery_unit_id');
        $manifest->notes            = $this->get('notes');
        $manifest->creator_id       = $user->id;
        $manifest->save();

        return $manifest;
    }
}
