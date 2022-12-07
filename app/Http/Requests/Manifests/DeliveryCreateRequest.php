<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;

class DeliveryCreateRequest extends ManifestCreateRequest
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
            'dest_network_id'  => 'required|numeric|exists:networks,id',
            'delivery_unit_id' => 'nullable|numeric|exists:users,id,role_id,7',
            'weight'           => 'nullable|numeric',
            'pcs_count'        => 'nullable|numeric',
            'notes'            => 'nullable|string|max:255',
        ];
    }

    public function persist()
    {
        $manifest = new Manifest;
        $manifest->number = $this->getNewManifestNumber('M2');
        $manifest->orig_network_id = auth()->user()->network_id;
        $manifest->dest_network_id = $this->get('dest_network_id');
        $manifest->delivery_unit_id = $this->get('delivery_unit_id');
        $manifest->type_id = 2;
        $manifest->weight = $this->get('weight');
        $manifest->pcs_count = $this->get('pcs_count');
        $manifest->notes = $this->get('notes');
        $manifest->creator_id = $this->user()->id;
        $manifest->save();

        return $manifest;
    }
}
