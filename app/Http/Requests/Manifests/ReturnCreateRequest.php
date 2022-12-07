<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;

class ReturnCreateRequest extends ManifestCreateRequest
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
            'dest_network_id' => 'required|numeric|exists:networks,id',
            'notes'           => 'nullable|string|max:255',
        ];
    }

    public function persist()
    {
        $manifest = new Manifest;
        $manifest->number = $this->getNewManifestNumber('M4');
        $manifest->orig_network_id = auth()->user()->network_id;
        $manifest->dest_network_id = $this->get('dest_network_id');
        $manifest->type_id = 4;
        $manifest->notes = $this->get('notes');
        $manifest->creator_id = $this->user()->id;
        $manifest->save();

        return $manifest;
    }
}
