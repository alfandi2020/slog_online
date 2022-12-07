<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;
use Illuminate\Foundation\Http\FormRequest;

abstract class ManifestCreateRequest extends FormRequest
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

    protected function getNewManifestNumber($prefix)
    {
        $criteria = $prefix;
        $criteria .= auth()->user()->network->code;
        $criteria .= date('ym');
        $lastManifest = Manifest::orderBy('id', 'desc')->where('number', 'like', $criteria.'%')->first();

        if ($lastManifest) {
            return ++$lastManifest->number;
        }

        return $criteria.'00001';
    }
}
