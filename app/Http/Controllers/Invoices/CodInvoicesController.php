<?php

namespace App\Http\Controllers\Invoices;

use App\Entities\Invoices\Cod as CodInvoice;
use App\Entities\Invoices\InvoicesRepository;
use App\Entities\Receipts\Receipt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;

class CodInvoicesController extends Controller
{
    private $repo;

    public function __construct(InvoicesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $status = 'proccess';
        if (in_array($request->get('status'), ['sent', 'paid', 'closed'])) {
            $status = $request->get('status');
        }

        $invoices = CodInvoice::where('network_id', auth()->user()->network_id)
            ->where(function ($query) use ($status) {
                if ($status == 'sent') {
                    return $query->isSent();
                } elseif ($status == 'paid') {
                    return $query->isPaid();
                } elseif ($status == 'closed') {
                    return $query->isVerified();
                } else {
                    return $query->isOnProccess();
                }
            })
            ->orderBy('date', 'desc')
            ->with('receipts', 'creator', 'handler')
            ->paginate(25);

        return view('invoices.cod.index', compact('invoices'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $invoices = $this->repo->getInvoicesByKeyword($query);
        return view('invoices.cod.search', compact('invoices'));
    }

    public function create()
    {
        $unInvoicedReceipts = collect([]);
        if (auth()->user()->isCustomerService()) {
            $unInvoicedReceipts = Receipt::orderBy('pickup_time', 'desc')
                ->where('payment_type_id', 3)
                ->whereNull('invoice_id')
                ->take(50)
                ->get();
        }
        return view('invoices.cod.create', compact('unInvoicedReceipts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'date'  => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $invoice = new CodInvoice;
        $invoice->number = $this->getNewInvoiceNumber();
        $invoice->type_id = 2;
        $invoice->periode = $request->get('date');
        $invoice->date = $request->get('date');
        $invoice->end_date = $request->get('date');
        $invoice->charge_details = [
            'discount'  => null,
            'admin_fee' => null,
        ];
        $invoice->amount = 0;
        $invoice->network_id = auth()->user()->network_id;
        $invoice->creator_id = auth()->id();
        $invoice->notes = $request->get('notes');
        $invoice->save();

        flash(trans('cod_invoice.created'), 'success');
        return redirect()->route('invoices.cod.show', [$invoice->id, 'action' => 'add_remove_receipt']);
    }

    public function show(CodInvoice $invoice)
    {
        $invoice->load('receipts');
        return view('invoices.cod.show', compact('invoice'));
    }

    public function edit(Request $request, CodInvoice $invoice)
    {
        if (!$request->user()->can('edit', $invoice)) {
            flash('invoice.uneditable', 'warning');
            return redirect()->route('invoices.cod.show', $invoice->id);
        }

        $invoice->load('receipts');
        $editableReceipt = null;
        if ($request->get('action') == 'receipt_edit' && $request->has('editable_receipt_id')) {
            $editableReceipt = Receipt::find($request->get('editable_receipt_id'));
        }

        return view('invoices.cod.edit', compact('invoice', 'editableReceipt'));
    }

    public function update(Request $request, CodInvoice $invoice)
    {
        $this->validate($request, [
            'date'  => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $invoice->periode = $request->get('date');
        $invoice->date = $request->get('date');
        $invoice->end_date = $request->get('date');
        $invoice->notes = $request->get('notes');

        $invoice->save();

        flash(trans('cod_invoice.updated', ['number' => $invoice->number]), 'success');
        return redirect()->route('invoices.cod.show', $invoice->id);
    }

    public function assignReceipt(Request $request, CodInvoice $invoice)
    {
        $this->validate($request, [
            'receipt_number_a' => 'required|exists:receipts,number',
        ], [
            'receipt_number_a.required' => 'No. Resi wajib diisi.',
            'receipt_number_a.exists'   => 'No. Resi tidak Valid.',
        ]);

        $receipt = Receipt::findByNumber($request->get('receipt_number_a'));

        if ($receipt->payment_type_id != '3') {
            flash(trans('cod_invoice.non_cod_receipt_addition_fails'), 'warning');
        } elseif ($receipt->hasStatusOf(['dl', 'bd'])) {
            flash(trans('cod_invoice.dl_bd_receipt_addition_fails'), 'warning');
        } else {
            $result = $invoice->assignReceipt($receipt);
            if ($result == false) {
                flash(trans('manifest.receipt_addition_fails'), 'warning');
            } else {
                flash(trans('manifest.receipt_added'), 'success');
            }
        }

        return back();
    }

    public function removeReceipt(Request $request, CodInvoice $invoice)
    {
        $this->validate($request, [
            'receipt_number_r' => 'required|exists:receipts,number',
        ], [
            'receipt_number_r.required' => 'No. Resi wajib diisi.',
            'receipt_number_r.exists'   => 'No. Resi tidak Valid.',
        ]);

        $receipt = Receipt::findByNumber($request->get('receipt_number_r'));
        $invoice->removeReceipt($receipt);

        flash(trans('manifest.receipt_removed'), 'success');

        return back();
    }

    public function deliver(Request $request, CodInvoice $invoice)
    {
        if (auth()->user()->can('send', $invoice)) {
            $invoice->send();
            flash(trans('cod_invoice.sent', ['number' => $invoice->number]), 'success');
        } else {
            flash(trans('cod_invoice.unsent', ['number' => $invoice->number]), 'warning');
        }

        return back();
    }

    public function undeliver(Request $request, CodInvoice $invoice)
    {
        if (auth()->user()->can('undeliver', $invoice)) {
            $invoice->takeBack();
            flash(trans('cod_invoice.has_taken_back', ['number' => $invoice->number]), 'success');
        } else {
            flash(trans('cod_invoice.cannot_taken_back', ['number' => $invoice->number]), 'warning');
        }

        return back();
    }

    public function verify(Request $request, CodInvoice $invoice)
    {
        if (auth()->user()->can('verify', $invoice)) {
            \DB::beginTransaction();
            $invoice->verify();
            $invoice->addPayment();
            \DB::commit();

            flash(trans('cod_invoice.verified', ['number' => $invoice->number]), 'success');
        } else {
            flash(trans('cod_invoice.cannot_verified', ['number' => $invoice->number]), 'warning');
        }
        return back();
    }

    public function destroy(Request $request, CodInvoice $invoice)
    {
        $this->authorize('delete', $invoice);
        if ($invoice->isSent()) {
            flash(trans('invoice.undeleted', ['number' => $invoice->number]), 'danger');
            return back();
        }

        $invoice->delete();

        flash(trans('invoice.deleted', ['number' => $invoice->number]), 'success');
        return redirect()->route('invoices.cod.index');
    }

    public function pdf(CodInvoice $invoice)
    {
        // return view('invoices.cod.pdf', compact('invoice'));

        $pdf = PDF::loadView('invoices.cod.pdf', compact('invoice'));
        return $pdf->stream($invoice->number.'.cod-invoice.pdf');
    }

    protected function getNewInvoiceNumber()
    {
        // format nomor : CSH aaaa bb cc xxxx
        // aaaa : nomor/kode cabang (4 digit)
        // bb : tahun
        // cc : bulan
        // xxxx : nomor urut (Nomor urut direset setiap awal tahun)
        // Contoh : CSH630017020010
        $prefix = 'COD';
        $networkCode = auth()->user()->network->code;
        $networkCode = substr($networkCode, 0, 4);
        $yearMonth = date('ym');

        $lastInvoice = CodInvoice::where('number', 'like', $prefix.$networkCode.$yearMonth.'%')->latest()->first();

        // TODO: rest counter every new year
        if (!is_null($lastInvoice)) {
            $currentNumber = substr($lastInvoice->number, -4);
            $currentNumber = $prefix.$networkCode.$yearMonth.$currentNumber;
            return ++$currentNumber;
        }

        return $prefix.$networkCode.$yearMonth.'0001';
    }
}
