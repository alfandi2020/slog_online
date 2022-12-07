<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;

class DistributionSendRequest extends ManifestCreateRequest
{
    private $manifest;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->manifest = Manifest::findOrFail($this->segment(3));
        return $this->user()->can('send-distribution', $this->manifest);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'deliver_at' => 'required|date_format:Y-m-d H:i',
            'start_km'   => 'nullable|numeric',
            'notes'      => 'nullable|string|max:255',
        ];
    }
}
