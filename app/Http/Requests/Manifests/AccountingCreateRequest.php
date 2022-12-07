<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;

class AccountingCreateRequest extends ManifestCreateRequest
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
            'customer_id' => 'required|numeric|exists:customers,id',
            'notes'       => 'nullable|string|max:255',
        ];
    }

    public function persist()
    {
        $manifest = new Manifest;
        $manifest->number = $this->getNewManifestNumber('M5');
        $manifest->customer_id = $this->get('customer_id');
        $manifest->orig_network_id = auth()->user()->network_id;
        $manifest->dest_network_id = auth()->user()->network_id;
        $manifest->type_id = 5;
        $manifest->notes = $this->get('notes');
        $manifest->creator_id = $this->user()->id;
        $manifest->save();

        return $manifest;
    }
}
