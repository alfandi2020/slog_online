<?php

namespace App\Http\Controllers\Manifests;

use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\ManifestsRepository;
use App\Entities\Manifests\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manifests\CreateRequest;
use DB;
use Illuminate\Http\Request;
use PDF;

class ManifestsController extends Controller
{
    private $repo;

    public function __construct(ManifestsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $manifests = $this->repo->searchByNumber($request->get('manifest_number'));
        return view('manifests.search', compact('manifests'));
    }

    public function create()
    {
        return view('manifests.create');
    }

    public function edit(Manifest $manifest)
    {
        return view('manifests.edit', compact('manifest'));
    }

    public function destroy(Manifest $manifest)
    {
        if ($manifest->isSent() || $manifest->isReceived()) {
            flash(trans('manifest.undeleted'), 'warning');
            return back();
        }

        DB::beginTransaction();
        $manifest->receipts()->detach();
        $manifest->delete();
        DB::commit();

        flash(trans('manifest.deleted'), 'warning');
        return redirect()->route('manifests.' . $manifest->pluralTypeCode() . '.index');
    }

    public function assignReceipt(Request $request, $manifestId)
    {
        $this->validate($request, [
            'receipt_number_a' => 'required|exists:receipts,number'
        ], [
            'receipt_number_a.required' => 'No. Resi wajib diisi.',
            'receipt_number_a.exists' => 'No. Resi tidak Valid.',
        ]);

        $assignReceiptResult = $this->repo->assignReceipt($manifestId, $request->get('receipt_number_a'));

        if ($assignReceiptResult == true)
            flash(trans('manifest.receipt_added'), 'success');
        else
            flash(trans('manifest.receipt_addition_fails'), 'warning');

        return back();
    }

    public function removeReceipt(Request $request, $manifestId)
    {
        $this->validate($request, [
            'receipt_number_r' => 'required|exists:receipts,number'
        ], [
            'receipt_number_r.required' => 'No. Resi wajib diisi.',
            'receipt_number_r.exists' => 'No. Resi tidak Valid.',
        ]);

        $removeReceiptResult = $this->repo->removeReceipt($manifestId, $request->get('receipt_number_r'));

        if ($removeReceiptResult == true)
            flash(trans('manifest.receipt_removed'), 'success');
        else
            flash(trans('manifest.receipt_removal_fails'), 'warning');

        return back();
    }

    public function send(Request $request, $manifestId)
    {
        $this->validate($request, [
            'manifest_number' => 'required|exists:manifests,number,id,' . $manifestId,
        ]);

        $result = $this->repo->sendManifest($manifestId);

        if ($result == false)
            flash(trans('manifest.unsent'), 'warning');
        else
            flash(trans('manifest.sent'), 'success');

        return back();
    }

    public function takeBack(Request $request, $manifestId)
    {
        $this->validate($request, [
            'manifest_number' => 'required|exists:manifests,number,id,' . $manifestId,
        ]);

        $result = $this->repo->takeManifestBack($manifestId);

        if ($result == false)
            flash(trans('manifest.cannot_taken_back'), 'warning');
        else
            flash(trans('manifest.has_taken_back'), 'success');

        return back();
    }

    public function receive(Manifest $manifest)
    {
        $manifest->load(['receipts.packType', 'receipts.origin', 'receipts.destination']);
        return view('manifests.receive', compact('manifest'));
    }

    public function checkReceipt(Request $request, $manifestId)
    {
        $this->validate($request, ['receipt_number_a' => 'required|exists:receipts,number']);

        $checkReceiptResult = $this->repo->checkReceipt($manifestId, $request->get('receipt_number_a'));

        if ($checkReceiptResult == true)
            flash(trans('manifest.receipt_pass'), 'success');
        else
            flash(trans('manifest.receipt_fails'), 'warning');

        return back();
    }

    public function rejectReceipt(Request $request, $manifestId)
    {
        $this->validate($request, ['receipt_number_r' => 'required|exists:receipts,number']);

        $rejectReceiptResult = $this->repo->rejectReceipt($manifestId, $request->get('receipt_number_r'));

        if ($rejectReceiptResult == true)
            flash(trans('manifest.receipt_rejected'), 'warning');
        else
            flash(trans('manifest.receipt_fails'), 'warning');

        return back();
    }

    public function patchReceive(Request $request, $manifestId)
    {
        $this->validate($request, [
            'manifest_number' => 'required|exists:manifests,number,id,' . $manifestId,
        ]);

        $manifest = $this->repo->receiveManifest($manifestId);

        if ($manifest == false) {
            flash(trans('manifest.cannot_received'), 'warning');
            return back();
        } else {
            flash(trans('manifest.received'), 'success');
            $type = $manifest->pluralTypeCode();
            return redirect()->route("manifests.$type.show", $manifest->number);
        }

    }
}
