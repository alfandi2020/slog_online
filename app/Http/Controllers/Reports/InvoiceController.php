<?php

namespace App\Http\Controllers\Reports;

use Excel;
use Illuminate\Http\Request;
use App\Entities\Invoices\Invoice;
use App\Http\Controllers\Controller;
use App\Entities\Invoices\InvoicesRepository;

class InvoiceController extends Controller
{
    private $repo;

    public function __construct(InvoicesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $status = 'proccess';
        if (in_array($request->get('status'), ['sent', 'paid', 'closed', 'problem'])) {
            $status = $request->get('status');
        }

        if ($request->get('export') == 'xls') {

            $invoices = $this->getInvoicesByStatus($status);

            // return view('reports.invoices.export-xls', compact('invoices'));
            Excel::create('list-invoice-'.$status.'-per-'.date('Y-m-d'), function ($excel) use ($invoices) {
                $excel->sheet('Export', function ($sheet) use ($invoices) {
                    $sheet->loadView('reports.invoices.export-xls', compact('invoices'));
                });
            })->export('xls');
        }

        $invoices = $this->repo->getCustomersInvoices($status);

        return view('reports.invoices.index', compact('invoices'));
    }

    public function accountReceivables()
    {
        $perDate = request('per_date', date('Y-m-d'));
        $invoices = $this->getAccountReceivablesReport($perDate);

        return view('reports.invoices.account-receivables', compact('invoices', 'perDate'));
    }

    private function getAccountReceivablesReport($perDate)
    {
        $invoiceQuery = Invoice::where('type_id', 2)
            ->orderBy('customer_id')
            ->with('customer', 'receipts')
            ->whereNotNull('sent_date')
            ->whereNull('problem_date');

        $invoiceQuery->where(function ($query) use ($perDate) {
            if ($perDate) {
                $query->where(function ($query) use ($perDate) {
                    $query->where('payment_date', '>', $perDate);
                    $query->orWhereNull('payment_date');
                });
                $query->where('date', '<=', $perDate);
            } else {
                $query->whereNull('payment_date');
            }
        });

        return $invoiceQuery->get();
    }

    private function getInvoicesByStatus($status)
    {
        return Invoice::where('network_id', auth()->user()->network_id)
            ->where(function ($query) use ($status) {
                if ($status == 'sent') {
                    return $query->isSent();
                } else if ($status == 'paid') {
                    return $query->isPaid();
                } else if ($status == 'closed') {
                    return $query->isVerified();
                } else if ($status == 'problem') {
                    return $query->isProblem();
                } else {
                    return $query->isOnProccess();
                }

            })
            ->where('type_id', 2)
            ->orderBy('date', 'desc')
            ->with('receipts', 'customer')
            ->get();
    }
}
