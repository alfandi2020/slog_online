<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;

class HandoverCreateRequest extends ManifestCreateRequest
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
            'weight'    => 'nullable|numeric',
            'pcs_count' => 'nullable|numeric',
            'notes'     => 'nullable|string|max:255',
        ];
    }

    public function persist()
    {
        $manifest = new Manifest;
        $manifest->number = $this->getNewManifestNumber('M1');
        $manifest->orig_network_id = auth()->user()->network_id;
        $manifest->dest_network_id = auth()->user()->network_id;
        $manifest->type_id = 1;
        $manifest->weight = $this->get('weight');
        $manifest->pcs_count = $this->get('pcs_count');
        $manifest->notes = $this->get('notes');
        $manifest->creator_id = $this->user()->id;
        $manifest->save();

        return $manifest;
    }
}
