<?php

namespace App\Http\Controllers\Receipts;

use DB;
use PDF;
use App\Entities\Users\User;
use Illuminate\Http\Request;
use App\Entities\Regions\City;
use App\Entities\Receipts\Receipt;
use App\Http\Controllers\Controller;
use App\Entities\Receipts\ReceiptsRepository;
use App\Http\Requests\Receipts\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class ReceiptsController extends Controller
{
    private $repo;

    public function __construct(ReceiptsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $receipts = $this->repo->getLatestReceipts($request->get('date'));
        return view('receipts.index', compact('receipts'));
    }

    public function search(Request $request)
    {
        $receipts = $this->repo->getReceiptsByNumber($request->get('query'));
        return view('receipts.search', compact('receipts'));
    }

    public function pdf(Receipt $receipt, $pagesCount = null)
    {
        $receiptDuplicates = $this->repo->getReceiptDuplicates($pagesCount);
        // return view('receipts.pdf.pdf', compact('receipt', 'receiptDuplicates'));

        $pdf = PDF::loadView('receipts.pdf.pdf', compact('receipt', 'receiptDuplicates'))
            ->setPaper([0, 0, 595.28, 280.63]);
        return $pdf->stream($receipt->number.'.e-receipt.pdf');
    }

    public function pdfV2(Receipt $receipt, $pagesCount = null)
    {
        $receiptDuplicates = $this->repo->getReceiptDuplicates($pagesCount);
        // return view('receipts.pdf.pdf_v2', compact('receipt', 'receiptDuplicates'));

        $pdf = PDF::loadView('receipts.pdf.pdf_v2', compact('receipt', 'receiptDuplicates'));

        return $pdf->stream($receipt->number.'.e-receipt.pdf');
    }

    public function pdfItemsLabel(Receipt $receipt)
    {
        if ($receipt && is_null($receipt->items_detail)) {
            flash('Tidak dapat mencetak Label Item karena Item tidak dirincikan.', 'danger');
            return redirect()->route('receipts.show', $receipt->number);
        }
        // return view('receipts.pdf.pdf-items-label', compact('receipt'));

        $pdf = PDF::loadView('receipts.pdf.pdf-items-label', compact('receipt'))
            ->setPaper([0, 0, 595.28, 280.63]);
            // ->setPaper('A4', 'landscape');
        return $pdf->stream($receipt->number.'.items-label.pdf');
    }

    public function show(Receipt $receipt)
    {
        return view('receipts.show', compact('receipt'));
    }

    public function items(Receipt $receipt)
    {
        return view('receipts.items', compact('receipt'));
    }

    public function progress(Receipt $receipt)
    {
        $receipt->progress->load('origin', 'destination', 'creator', 'handler', 'manifest.creator');
        if ($receipt->has('invoice')) {
            $receipt->load('invoice');
        }
        return view('receipts.progress', compact('receipt'));
    }

    public function manifests(Receipt $receipt)
    {
        $receipt->manifests->load('originNetwork', 'destinationNetwork', 'creator', 'handler');
        return view('receipts.manifests', compact('receipt'));
    }

    public function pod(Receipt $receipt)
    {
        if (request('action') === 'edit') {
            $couriers = $this->getCouriersList();
        }
        return view('receipts.pod', compact('receipt', 'couriers'));
    }

    public function edit(Receipt $receipt)
    {
        try {
            $this->authorize('edit', $receipt);
        } catch (AuthorizationException $e) {
            flash(trans('receipt.uneditable'), 'warning');
            return redirect()->route('receipts.show', $receipt->number);
        }

        $cityOrigin = $receipt->origCity;

        $destinationCities = $this->getDestinationCitiesListOf($cityOrigin);
        $destinationDistricts = $this->getDestinationDistrictsListOf($cityOrigin, $receipt->dest_city_id);
        $availableCourierList = $this->getCouriersList();

        return view(
            'receipts.edit',
            compact(
                'receipt', 'cityOrigin', 'destinationCities',
                'destinationDistricts', 'availableCourierList'
            )
        );
    }

    public function update(UpdateRequest $updateForm, Receipt $receipt)
    {
        $updateForm->persist();

        flash(trans('receipt.updated'));
        return redirect()->route('receipts.show', $receipt->number);
    }

    public function delete(Receipt $receipt)
    {
        return view('receipts.delete', compact('receipt'));
    }

    public function destroy(Request $request, Receipt $receipt)
    {
        try {
            $this->authorize('edit', $receipt);
        } catch (AuthorizationException $e) {
            flash(trans('receipt.undeletable'), 'warning');
            return redirect()->route('receipts.show', $receipt->number);
        }

        $this->validate($request, [
            'notes' => 'required|max:255',
        ], [
            'notes.required' => 'Alasan penghapusan wajib diisi.',
        ]);

        \DB::beginTransaction();
        $receipt->notes = $request->get('notes');
        $oldReceiptNumber = $receipt->number;
        if (strlen($oldReceiptNumber) < 18) {
            $receipt->number = $oldReceiptNumber.'_';
        }
        $receipt->save();
        $receipt->delete();

        \DB::commit();

        flash(trans('receipt.deleted'), 'warning');
        return redirect()->route('receipts.search');
    }

    protected function getDestinationCitiesListOf(City $city)
    {
        $destinationCities = [];
        foreach ($city->destinationCities()->with('province')->get() as $city) {
            $destinationCities[$city->province->name][$city->id] = $city->name;
        }
        return $destinationCities;
    }

    protected function getDestinationDistrictsListOf(City $city, $destCityId)
    {
        $destinationDistricts = [];
        foreach ($city->destinationDistricts()->where('dest_city_id', $destCityId)->get() as $district) {
            $destinationDistricts[$district->id] = $district->name;
        }
        return $destinationDistricts;
    }

    protected function getCouriersList()
    {
        return \App\Entities\Users\User::where([
            'network_id' => auth()->user()->network_id,
            'role_id'    => 7,
        ])
            ->pluck('name', 'id');
    }

    public function problemNotesUpdate(Request $request)
    {
        $this->validate($request, [
            'manifest_id' => 'required|numeric|exists:manifests,id',
            'notes.*'     => 'nullable|string|max:255',
        ]);

        $updatedCount = 0;
        DB::beginTransaction();
        foreach ($request->get('notes') as $receiptId => $note) {
            DB::table('receipt_progress')
                ->where('manifest_id', $request->get('manifest_id'))
                ->where('receipt_id', $receiptId)
                ->update(['notes' => $note, 'updated_at' => date('Y-m-d H:i:s')]);

            $updatedCount++;
        }
        DB::commit();

        flash(trans('receipt.problem_notes_updated', ['count' => $updatedCount]), 'info');

        return redirect()->route('manifests.problems.show', $request->get('manifest_number'));
    }

    public function costsDetail(Receipt $receipt)
    {
        $rate = $receipt->rate;

        return view('receipts.costs-detail', compact('receipt', 'rate'));
    }

    public function couriers(Receipt $receipt)
    {
        return view('receipts.couriers', compact('receipt'));
    }

    public function customerUpdate(Request $request, Receipt $receipt)
    {
        $this->validate($request, [
            'customer_id'     => 'nullable',
            'payment_type_id' => 'required|numeric|in:1,2,3',
        ]);

        $receipt->customer_id = $request->get('customer_id');
        $receipt->payment_type_id = $request->get('payment_type_id');
        $receipt->save();

        flash(trans('receipt.customer_updated'), 'success');

        return back();
    }
}
