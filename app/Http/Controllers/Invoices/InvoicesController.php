<?php

namespace App\Http\Controllers\Invoices;

use PDF;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Entities\Invoices\Invoice;
use App\Entities\Receipts\Receipt;
use App\Entities\Customers\Customer;
use App\Http\Controllers\Controller;
use App\Entities\Transactions\Transaction;
use App\Entities\Transactions\PaymentMethod;
use App\Entities\Invoices\InvoicesRepository;
use App\Http\Requests\Invoices\CreateRequest;
use App\Http\Requests\Invoices\UpdateRequest;

class InvoicesController extends Controller
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

        $invoices = $this->repo->getCustomersInvoices($status);
        return view('invoices.index', compact('invoices'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $invoices = $this->repo->getInvoicesByKeyword($query);
        return view('invoices.search', compact('invoices'));
    }

    public function customerList()
    {
        $customers = $this->repo->getCustomerList();
        return view('invoices.customer-list', compact('customers'));
    }

    public function create(Customer $customer)
    {
        return view('invoices.create', compact('customer'));
    }

    public function store(CreateRequest $request, Customer $customer)
    {
        $invoice = $this->repo->createInvoiceFor($customer, $request->only(
            'periode', 'date', 'end_date', 'discount',
            'admin_fee', 'notes', 'receipt_id'
        ));

        flash(trans('invoice.created'), 'success');
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('receipts.destination');
        $paymentMethods = PaymentMethod::pluck('name', 'id')->all();
        $paymentTypes = [trans('transaction.types.out'), trans('transaction.types.in')];
        $editableTransaction = null;

        if (request('action') == 'edit_payment' && request('id')) {
            $editableTransaction = Transaction::find(request('id'));
        }

        return view('invoices.show', compact('invoice', 'paymentMethods', 'paymentTypes', 'editableTransaction'));
    }

    public function edit(Request $request, Invoice $invoice)
    {
        $invoice->load('receipts.destination');
        // $invoice->load('receipts.destination', 'receipts.rate');

        $editableReceipt = null;
        if ($request->get('action') == 'receipt_edit' && $request->has('editable_receipt_id')) {
            $editableReceipt = Receipt::find($request->get('editable_receipt_id'));
        }

        $accountingUsersList = $this->repo->getAccountingUsersList(auth()->user()->network_id);

        return view('invoices.edit', compact('invoice', 'editableReceipt', 'accountingUsersList'));
    }

    public function update(UpdateRequest $request, Invoice $invoice)
    {
        $invoice->periode = $request->get('periode');
        $invoice->date = $request->get('date');
        $invoice->end_date = $request->get('end_date');
        $invoice->creator_id = $request->get('creator_id');
        $invoice->notes = $request->get('notes');

        $chargeDetails = $invoice->charge_details;
        $chargeDetails['discount'] = $request->get('discount');
        $chargeDetails['admin_fee'] = $request->get('admin_fee');
        $invoice->charge_details = $chargeDetails;

        $invoice->save();

        $invoice->recalculateAmount();

        flash(trans('invoice.updated', ['number' => $invoice->number]), 'success');
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function assignReceipt(Request $request, Invoice $invoice)
    {
        $this->validate($request, [
            'receipt_number_a' => 'required|exists:receipts,number',
        ], [
            'receipt_number_a.required' => 'No. Resi wajib diisi.',
            'receipt_number_a.exists'   => 'No. Resi tidak Valid.',
        ]);

        $receipt = Receipt::findByNumber($request->get('receipt_number_a'));

        if ($receipt->payment_type_id != '2') {
            flash(trans('invoice.non_credit_receipt_addition_fails'), 'danger');
        } else {
            $invoice->assignReceipt($receipt);
            flash(trans('manifest.receipt_added'), 'success');
        }

        return back();
    }

    public function removeReceipt(Request $request, Invoice $invoice)
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

    public function deliver(Request $request, Invoice $invoice)
    {
        $invoice->send();

        flash(trans('invoice.sent', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function undeliver(Request $request, Invoice $invoice)
    {
        $invoice->takeBack();

        flash(trans('invoice.has_taken_back', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function paymentEntry(Request $request, Invoice $invoice)
    {
        $yesterday = Carbon::createFromFormat('Y-m-d', $invoice->sent_date)->subDay()->format('Y-m-d');
        $this->validate($request, [
            'payment_date'      => 'required|date_format:Y-m-d|after:'.$yesterday,
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required|numeric|exists:payment_methods,id',
            'notes'             => 'nullable|string|max:255',
        ]);

        \DB::beginTransaction();

        $invoice->payment_date = $request->get('payment_date');
        $invoice->save();

        $transaction = new Transaction;
        $transaction->number = $transaction->generateNumber();
        $transaction->date = $request->get('payment_date');
        $transaction->in_out = 1;
        $transaction->amount = $request->get('amount');
        $transaction->creator_id = auth()->id();
        $transaction->payment_method_id = $request->get('payment_method_id');
        $transaction->notes = $request->get('notes');
        $invoice->payments()->save($transaction);

        \DB::commit();

        flash(trans('invoice.paid', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function setProblem(Request $request, Invoice $invoice)
    {
        $this->authorize('set-problem', $invoice);
        $invoice->setProblemDate(Carbon::now()->format('Y-m-d'));

        flash(trans('invoice.problem', ['number' => $invoice->number]), 'warning');
        return back();
    }

    public function unsetProblem(Request $request, Invoice $invoice)
    {
        $this->authorize('unset-problem', $invoice);
        $invoice->setProblemDate(null);

        flash(trans('invoice.'.$invoice->present()->status['code'], ['number' => $invoice->number]), 'warning');
        return back();
    }

    public function setPaid(Request $request, Invoice $invoice)
    {
        // TODO: only invoice that has payments that can be set set as paid.
        $invoice->setPaymentDate(Carbon::now()->format('Y-m-d'));

        flash(trans('invoice.paid', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function setUnpaid(Request $request, Invoice $invoice)
    {
        $invoice->setPaymentDate(null);

        flash(trans('invoice.unpaid', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function verify(Request $request, Invoice $invoice)
    {
        $invoice->verify();

        flash(trans('invoice.verified', ['number' => $invoice->number]), 'success');
        return back();
    }

    public function delete(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        return view('invoices.delete', compact('invoice'));
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        if ($invoice->isSent()) {
            flash(trans('invoice.undeleted', ['number' => $invoice->number]), 'danger');
            return back();
        }

        $invoice->delete();

        flash(trans('invoice.deleted', ['number' => $invoice->number]), 'success');
        return redirect()->route('customers.invoices', $invoice->customer_id);
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('receipts.destCity', 'receipts.destDistrict');
        $bankAccounts = PaymentMethod::where('id', '>', 1)->where('is_active', 1)->get();
        // return view('invoices.pdf', compact('invoice'));

        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'bankAccounts'));
        return $pdf->stream($invoice->number.'.e-invoice.pdf');
    }

    public function exportXls(Invoice $invoice)
    {
        $invoice->load('receipts.packType', 'receipts.origCity', 'receipts.origDistrict', 'receipts.destCity', 'receipts.destDistrict', 'receipts.deliveryCourier');
        // return view('invoices.export-xls', compact('invoice'));

        Excel::create('Invoice No. '.$invoice->number, function ($excel) use ($invoice) {
            $excel->sheet('Export', function ($sheet) use ($invoice) {
                // $sheet->setColumnFormat(['C' => '@']);
                $sheet->loadView('invoices.export-xls', compact('invoice'));
            });
        })->export('xls');
    }

    public function receiptCostUpdate(Request $request, $receiptId)
    {
        if ($request->has('recalculate_bill_amount')) {
            $receipt = Receipt::findOrFail($receiptId);

            $costsDetail = $receipt->costs_detail;
            $costsDetail['admin_fee'] = (int) $request->get('admin_fee');
            $receipt->costs_detail = $costsDetail;

            $receipt->recalculateBillAmount();

            $receipt->invoice->recalculateAmount();

            flash(trans('receipt.cost_detail_updated', ['number' => $receipt->number]), 'success');

            session()->flash('last_updated_id', $receipt->id);
            return redirect()->route('invoices.edit', [$receipt->invoice_id, '#'.$receipt->id]);
        }

        $this->validate($request, [
            'base_rate'           => 'required|numeric|min:0',
            'base_charge'         => 'required|numeric|min:0',
            'discount'            => 'required|numeric|min:0',
            'packing_cost'        => 'required|numeric|min:0',
            'insurance_cost'      => 'required|numeric|min:0',
            'add_cost'            => 'required|numeric|min:0',
            'admin_fee'           => 'required|numeric|min:0',
            'weight'              => 'required|numeric|min:1',
            'items_count'         => 'required|numeric|min:1',
            'pcs_count'           => 'required|numeric|min:1',
            'customer_invoice_no' => 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:255',
        ]);

        $receipt = Receipt::findOrFail($receiptId);
        $receipt->weight = $request->get('weight');
        $receipt->pcs_count = $request->get('pcs_count');
        $receipt->base_rate = $request->get('base_rate');
        $receipt->items_count = $request->get('items_count');
        $receipt->customer_invoice_no = cleanUpCustomerInvoiceNo($request->get('customer_invoice_no'));
        $receipt->notes = $request->get('notes');

        $costsDetail = $this->getCostDetail($request);
        $receipt->costs_detail = $costsDetail;

        $receipt->bill_amount = $costsDetail['total'];
        $receipt->save();

        $receipt->invoice->recalculateAmount();

        flash(trans('receipt.cost_detail_updated', ['number' => $receipt->number]), 'success');
        session()->flash('last_updated_id', $receipt->id);
        return redirect()->route('invoices.edit', $receipt->invoice_id);
    }

    private function getCostDetail($request)
    {
        $subtotal = $request->get('base_charge') - $request->get('discount');
        $total = $subtotal
         + $request->get('packing_cost')
         + $request->get('insurance_cost')
         + $request->get('add_cost')
         + $request->get('admin_fee');

        return [
            'base_charge'    => (int) $request->get('base_charge'),
            'discount'       => (int) $request->get('discount'),
            'subtotal'       => (int) $subtotal,
            'packing_cost'   => (int) $request->get('packing_cost'),
            'insurance_cost' => (int) $request->get('insurance_cost'),
            'add_cost'       => (int) $request->get('add_cost'),
            'admin_fee'      => (int) $request->get('admin_fee'),
            'total'          => (int) $total,
        ];
    }

    public function updateDeliveryInfo(Invoice $invoice)
    {
        $yesterday = Carbon::createFromFormat('Y-m-d', $invoice->sent_date)->subDay()->format('Y-m-d');

        $deliveryData = request()->validate([
            'consignor'     => 'nullable|string',
            'consignee'     => 'nullable|string',
            'received_date' => 'nullable|date_format:Y-m-d|after:'.$yesterday,
        ]);

        $invoice->received_date = $deliveryData['received_date'];
        $deliveryInfo['consignor'] = $deliveryData['consignor'];
        $deliveryInfo['consignee'] = $deliveryData['consignee'];
        $invoice->delivery_info = $deliveryInfo;

        $invoice->save();

        flash(trans('invoice.delivery_updated', ['number' => $invoice->number]), 'success');

        // return redirect()->route('invoices.show', $invoice->id);
        return back();
    }
}
