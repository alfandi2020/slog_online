<?php

namespace App\Http\Controllers\Manifests;

use App\Entities\Manifests\AccountingsRepository;
use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Receipt;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manifests\AccountingCreateRequest;
use Excel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use PDF;

class AccountingsController extends Controller
{
    private $repo;

    public function __construct(AccountingsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $manifests = $this->repo->getLatestManifests();
        return view('manifests.accountings.index', compact('manifests'));
    }

    public function create()
    {
        return view('manifests.accountings.create');
    }

    public function store(AccountingCreateRequest $manifestForm)
    {
        $manifest = $manifestForm->persist();
        flash(trans('manifest.created'), 'success');
        return redirect()->route('manifests.accountings.show', $manifest->number);
    }

    public function show(Manifest $manifest)
    {
        $manifest->load(['receipts.packType', 'receipts.origin', 'receipts.destination']);
        return view('manifests.accountings.show', compact('manifest'));
    }

    public function edit(Manifest $manifest)
    {
        try {
            $this->authorize('edit', $manifest);
        } catch (AuthorizationException $e) {
            flash(trans('manifest.uneditable'), 'warning');
            return redirect()->route('manifests.accountings.show', $manifest->number);
        }

        return view('manifests.accountings.edit', compact('manifest'));
    }

    public function update(Request $request, Manifest $manifest)
    {
        $this->validate($request, [
            'customer_id' => 'required|numeric|exists:customers,id',
            'notes'       => 'nullable|string|max:255',
        ]);

        $manifest->customer_id = $request->get('customer_id');
        $manifest->notes = $request->get('notes');
        $manifest->save();

        flash(trans('manifest.updated'));

        return redirect()->route('manifests.accountings.show', $manifest->number);
    }

    public function assignReceipt(Request $request, $manifestId)
    {
        $this->validate($request, [
            'receipt_number_a' => 'required|exists:receipts,number',
        ], [
            'receipt_number_a.required' => 'No. Resi wajib diisi.',
            'receipt_number_a.exists'   => 'No. Resi tidak Valid.',
        ]);

        $receipt = Receipt::where('number', $request->get('receipt_number_a'))->firstOrFail();

        if ($receipt->hasPaymentType(['cash', 'cod'])) {
            flash(trans('manifest.accountings.non_credit_receipt_addition_fails'), 'warning');
            return back();
        }

        $manifest = $this->repo->requireById($manifestId);

        if ($receipt->customer_id !== $manifest->customer_id) {
            flash(trans('manifest.accountings.different_customer_receipt_addition_fails'), 'warning');
            return back();
        }

        if ($receipt->hasStatusOf(['dl', 'bd'])) {
            flash(trans('manifest.accountings.dl_bd_receipt_addition_fails'), 'warning');
            return back();
        }

        $assignReceiptResult = $manifest->addReceipt($receipt);

        if ($assignReceiptResult == true) {
            flash(trans('manifest.receipt_added'), 'success');
        } else {
            flash(trans('manifest.receipt_addition_fails'), 'warning');
        }

        return back();
    }

    public function pdf(Manifest $manifest)
    {
        // return view('manifests.accountings.pdf', compact('manifest'));

        $pdf = PDF::loadView('manifests.accountings.pdf', compact('manifest'));
        return $pdf->stream($manifest->number.'.e-manifest.pdf');
    }

    public function html(Manifest $manifest)
    {
        return view('manifests.accountings.html', compact('manifest'));
    }

    public function excel(Manifest $manifest)
    {
        // return view('manifests.accountings.html', compact('manifest'));
        Excel::create('Export '.$manifest->number, function ($excel) use ($manifest) {
            $excel->sheet('Manifest', function ($sheet) use ($manifest) {
                $sheet->loadView('manifests.accountings.html', compact('manifest', 'receiptQuery'));
            });
        })->export('xls');
    }
}
