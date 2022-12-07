<?php

namespace App\Http\Controllers\Receipts;

use Session;
use Illuminate\Http\Request;
use App\Entities\Receipts\Receipt;
use App\Entities\Receipts\Progress;
use App\Http\Controllers\Controller;

class ReturningsController extends Controller
{
    public function index()
    {
        $returningReceipts = [];
        $returningReceiptNumbers = session('receipts.returnings');

        if (is_array($returningReceiptNumbers)) {
            $returningReceipts = Receipt::whereIn('number', $returningReceiptNumbers)->get();
        }

        return view('receipts.returnings.index', compact('returningReceipts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receipt_number_a' => 'required|exists:receipts,number',
        ], [
            'receipt_number_a.exists' => trans('receipt.not_found'),
        ]);

        $receipt = Receipt::where('number', $request->get('receipt_number_a'))->firstOrFail();

        if ($receipt->hasStatusOf(['dl', 'bd']) == false) {
            flash(trans('manifest.receipt_addition_fails'), 'danger');

            return back();
        }

        if (auth()->user()->network_id != 1) {
            if (auth()->user()->network_id != $receipt->network_id) {
                $message = __('receipt.return_entry_fail_network', [
                    'receipt' => $receipt->number,
                    'network' => auth()->user()->network->code_name,
                ]);

                flash($message, 'warning');

                return back();
            }
        }

        $returningReceiptNumbers = session('receipts.returnings');

        if (is_array($returningReceiptNumbers)) {
            $returningReceiptNumbers[] = $request->get('receipt_number_a');
        } else {
            $returningReceiptNumbers = [$request->get('receipt_number_a')];
        }

        session(['receipts.returnings' => array_unique($returningReceiptNumbers)]);

        flash(trans('manifest.receipt_added'), 'success');

        return back();
    }

    public function remove(Request $request)
    {
        $request->validate([
            'receipt_number_r' => 'required|exists:receipts,number',
        ], [
            'receipt_number_r.exists' => trans('receipt.not_found'),
        ]);

        $receiptNumberToRemove = $request->get('receipt_number_r');
        $returningReceiptNumbers = collect(session('receipts.returnings'));
        if ($returningReceiptNumbers->contains($receiptNumberToRemove)) {
            $filtered = $returningReceiptNumbers->reject(function ($value, $key) use ($receiptNumberToRemove) {
                return $value == $receiptNumberToRemove;
            });

            session(['receipts.returnings' => $filtered->all()]);
            flash(trans('manifest.receipt_removed'), 'warning');
        } else {
            flash(trans('receipt.not_found'), 'danger');
        }

        return back();
    }

    public function destroy()
    {
        session()->forget('receipts.returnings');

        flash(trans('receipt.returnings_destroyed'), 'warning');

        return back();
    }

    public function setAllReturned(Request $request)
    {
        $requestData = $request->validate(['returned_time' => 'required|date_format:Y-m-d H:i']);

        $returningReceiptNumbers = session('receipts.returnings');

        if (is_array($returningReceiptNumbers)) {
            \DB::beginTransaction();
            $currentUserId = auth()->id();
            Receipt::whereIn('number', $returningReceiptNumbers)->update(['status_code' => 'rt']);
            $receipts = Receipt::whereIn('number', $returningReceiptNumbers)->get();
            foreach ($receipts as $receipt) {
                $distributionManifestId = $receipt->distributionManifest()->id;
                Progress::where('receipt_id', $receipt->id)
                    ->where('manifest_id', $distributionManifestId)
                    ->update([
                        'handler_id' => $currentUserId,
                        'end_status' => 'rt',
                        'updated_at' => $requestData['returned_time'].':00',
                    ]);
            }
            session()->forget('receipts.returnings');
            \DB::commit();

            flash(trans('receipt.all_returned'), 'success');
            return back();
        }

        flash(trans('receipt.returning_fails'), 'error');
        return back();
    }
}
