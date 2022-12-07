<?php

namespace App\Http\Controllers\Receipts;

use DB;
use Carbon\Carbon;
use App\Entities\Users\User;
use Illuminate\Http\Request;
use App\Entities\Receipts\Proof;
use App\Entities\Receipts\Receipt;
use App\Entities\Receipts\Progress;
use App\Entities\Manifests\Manifest;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class PodsController extends Controller
{
    public function byManifest(Request $request)
    {
        $info = 'Masukkan ' . __('manifest.number') . ' atau scan Barcode Manifest';
        $infoClass = 'info';
        $receipt = null;
        $manifest = null;
        $couriers = [];
        if ($request->get('manifest_number')) {
            $manifest = Manifest::findByNumber($request->get('manifest_number'));
            if (is_null($manifest)) {
                $infoClass = 'warning';
                $info = __('pod.manifest_not_found', ['number' => $request->get('manifest_number')]);
            }

            if (!is_null($manifest) && $manifest->type_id !== 3) {
                $infoClass = 'warning';
                $info = __('pod.manifest_invalid', ['number' => $request->get('manifest_number'), ['type' => $manifest->type()]]);
                $manifest = null;
            }

            if ($request->get('receipt_number')) {
                $receipt = Receipt::findByNumber($request->get('receipt_number'));
                $couriers = $this->getCouriersList();
            }
        }

        return view('pods.by-manifest', compact('manifest', 'receipt', 'couriers', 'info', 'infoClass'));
    }

    public function store(Request $request, $receiptId)
    {
        $podData = $request->validate([
            'manifest_id'         => 'exists:receipt_progress,manifest_id,receipt_id,' . $receiptId,
            'number'              => 'required|numeric',
            'delivery_courier_id' => 'required|numeric|exists:users,id',
            'time'                => 'required|date_format:Y-m-d H:i',
            'status_code'         => 'required|string',
            'recipient'           => 'required_if:status_code,dl,bd|nullable|string|max:60',
            'notes'               => 'nullable|string|max:255',
            'customer_invoice_no' => 'nullable|string|max:255',
            'image_proof'         => 'nullable|file|mimes:jpg,jpeg|max:500',
        ]);

        $receipt = Receipt::where([
            'number' => $podData['number'],
            'id'     => $receiptId,
        ])->first();

        $progress = Progress::where([
            'manifest_id' => $podData['manifest_id'],
            'receipt_id'  => $receiptId,
        ])->first();

        if (is_null($receipt) || is_null($progress)) {
            flash(__('receipt.invalid'), 'danger');
            return back();
        }

        if ($request->hasFile('image_proof')) {
            $filenameWithExt = $request->file('image_proof')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image_proof')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('image_proof')->storeAs('public/imgs/pod', $filenameSimpan);
        } else {
            $filenameSimpan = 'noProofImage.jpg';
        }

        DB::beginTransaction();
        // Update the receipt
        $consignee = $receipt->consignee;
        $consignee['recipient'] = $podData['recipient'];
        $receipt->consignee = $consignee;
        $receipt->status_code = $podData['status_code'];
        $receipt->delivery_courier_id = $podData['delivery_courier_id'];
        $receipt->customer_invoice_no = cleanUpCustomerInvoiceNo($podData['customer_invoice_no']);
        $receipt->image_proof = $filenameSimpan;
        $receipt->save();

        // Update the receipt progress
        $progress->updated_at = $podData['time'] . ':00';
        $customerService = auth()->user();
        $progress->handler_id = $customerService->id;
        $progress->handler_location_id = $customerService->network->origin->id;
        $progress->end_status = $podData['status_code'];
        $progress->notes = $podData['notes'];
        $progress->save();

        // Add Delivery Proof if it receipt has delivered
        if ($receipt->hasStatusOf(['dl', 'bd'])) {
            $customerService = auth()->user();

            $proof = new Proof;
            $proof->progress_id = $progress->id;
            $proof->receipt_id = $receipt->id;
            $proof->manifest_id = $progress->manifest_id;
            $proof->courier_id = $podData['delivery_courier_id'];
            $proof->creator_id = $customerService->id;
            $proof->location_id = $customerService->network->origin->id;
            $proof->status_code = $podData['status_code'];
            $proof->recipient = $podData['recipient'];
            $proof->delivered_at = $podData['time'] . ':00';
            $proof->notes = $podData['notes'];
            $proof->save();
        }
        DB::commit();

        flash(__('pod.created'));
        return redirect()->route('pods.by-manifest', ['manifest_number' => $progress->manifest->number]);
    }

    public function receiveManifest(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'deliver_at'  => 'required|date_format:Y-m-d H:i',
            'received_at' => 'required|date_format:Y-m-d H:i',
            'start_km'    => 'nullable|numeric',
            'end_km'      => 'required_with:start_km|nullable|numeric',
        ]);

        DB::beginTransaction();

        $progressUpdate = [];

        foreach ($manifest->receipts->pluck('pivot')->toArray() as $progress) {
            $progressUpdate[] = $progress['id'];
        }

        DB::table('receipt_progress')->whereIn('id', $progressUpdate)->update([
            'end_status' => 'rt',
            'updated_at' => Carbon::now(),
        ]);

        $manifest->deliver_at = $request->deliver_at . ':00';
        $manifest->received_at = $request->received_at . ':00';
        $manifest->start_km = $request->start_km;
        $manifest->end_km = $request->end_km;
        $manifest->notes = $request->notes;
        $manifest->save();

        DB::commit();

        flash(__('manifest.received'), 'success');
        return redirect()->route('pods.by-manifest', ['manifest_number' => $manifest->number]);
    }

    public function byReceipt(Request $request)
    {
        $info = 'Masukkan ' . __('receipt.number') . ' atau scan ' . __('receipt.barcode');
        $infoClass = 'info';
        $couriers = [];
        $manifest = null;
        $receipt = Receipt::findByNumber($request->get('receipt_number'));

        if ($receipt) {
            if ($receipt->isDelivered()) {
                flash(__('pod.cannot_enter_receipt_pod', ['receipt_number' => $receipt->number]), 'danger');
                return redirect()->route('pods.by-receipt');
            }

            $manifest = $receipt->manifests()->where('type_id', 3)->latest()->first();
            if (is_null($manifest) || $manifest->isSent() == false) {
                flash(__('pod.cannot_enter_receipt_pod', ['receipt_number' => $receipt->number]), 'danger');
                return redirect()->route('pods.by-receipt');
            }
            $couriers = $this->getCouriersList();
        } else {
            $info = __('receipt.not_found');
            $infoClass = 'warning';
        }

        return view('pods.by-receipt', compact('manifest', 'receipt', 'couriers', 'info', 'infoClass'));
    }

    protected function getCouriersList()
    {
        return User::where([
            'network_id' => auth()->user()->network_id,
            'role_id'    => 7,
        ])
            ->pluck('name', 'id');
    }

    public function update(Request $request, Receipt $receipt, Proof $proof)
    {
        try {
            $this->authorize('edit-pod', $receipt);
        } catch (AuthorizationException $e) {
            flash(__('pod.uneditable'), 'warning');
            return redirect()->route('receipts.pod', $receipt->number);
        }

        $podData = $request->validate([
            'delivery_courier_id' => 'required|numeric|exists:users,id',
            'time'                => 'required|date_format:Y-m-d H:i',
            'status_code'         => 'required|string',
            'recipient'           => 'required_if:status_code,dl,bd|nullable|string|max:60',
            'notes'               => 'nullable|string|max:255',
            'customer_invoice_no' => 'nullable|string|max:255',
            'image_proof'         => 'nullable|file|mimes:jpg,jpeg|max:5120',
        ]);

        if ($request->hasFile('image_proof')) {
            $filenameWithExt = $request->file('image_proof')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image_proof')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('image_proof')->storeAs('public/imgs/pod', $filenameSimpan);
        } else {
            $filenameSimpan = 'noProofImage.jpg';
        }

        $time = $podData['time'] . ':00';

        \DB::beginTransaction();
        $consignee = $receipt->consignee;
        $consignee['recipient'] = $podData['recipient'];
        $receipt->consignee = $consignee;
        $receipt->status_code = $podData['status_code'];
        $receipt->delivery_courier_id = $podData['delivery_courier_id'];
        $receipt->customer_invoice_no = cleanUpCustomerInvoiceNo($podData['customer_invoice_no']);
        $receipt->image_proof = $filenameSimpan;
        $receipt->save();

        // $progress->updated_at = $time;
        // // $customerService = auth()->user();
        // // $progress->handler_id = $customerService->id;
        // // $progress->handler_location_id = $customerService->network->origin->id;
        // $progress->end_status = $podData['status_code'];
        // $progress->notes = $podData['notes'];
        // $progress->save();

        $proof = $receipt->proof;
        $proof->courier_id = $podData['delivery_courier_id'];
        $proof->status_code = $podData['status_code'];
        $proof->recipient = $podData['recipient'];
        $proof->delivered_at = $time;
        $proof->notes = $podData['notes'];
        $proof->save();

        \DB::commit();

        flash(__('pod.updated'));

        return redirect()->route('receipts.pod', $receipt->number);
    }
}
