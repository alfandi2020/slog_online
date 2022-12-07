<?php

namespace App\Http\Requests\Manifests;

use App\Entities\Manifests\Manifest;
use DB;

class ProblemCreateRequest extends ManifestCreateRequest
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
            'receipt_id' => 'required',
            'handler_id' => 'required|numeric|exists:users,id',
            'notes'      => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'receipt_id.required' => 'Resi harus dipilih.'
        ];
    }

    public function persist()
    {
        $authUser = $this->user();

        $manifest = new Manifest;
        $manifest->number = $this->getNewManifestNumber('M6');
        $manifest->type_id = 6;
        $manifest->orig_network_id = $authUser->network_id;
        $manifest->dest_network_id = $authUser->network_id;
        $manifest->creator_id = $authUser->id;
        $manifest->handler_id = $this->get('handler_id');
        $manifest->notes = $this->get('notes');
        $manifest->save();

        $progressData = [];
        foreach ($this->get('receipt_id') as $receiptId) {
            $progressData[] = [
                'receipt_id' => $receiptId,
                'manifest_id' => $manifest->id,
                'notes' => null,
                'creator_id' => $authUser->id,
                'creator_location_id' => $authUser->network->origin->id,
                'start_status' => 'pr',
                'handler_id' => null,
                'handler_location_id' => null,
                'end_status' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('receipt_progress')->insert($progressData);

        return $manifest;
    }
}
