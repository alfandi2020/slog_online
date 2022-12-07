<?php

namespace App\Entities\Invoices;

use DB;
use App\Entities\Users\User;
use App\Entities\BaseRepository;
use App\Entities\Receipts\Receipt;
use App\Entities\Customers\Customer;
use Illuminate\Database\Eloquent\Model;

class InvoicesRepository extends BaseRepository
{
    protected $model;

    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    public function getInvoicesByKeyword($q)
    {
        if (is_null($q)) {
            return [];
        }

        return $this->model->where('network_id', auth()->user()->network_id)
            ->where(function ($query) use ($q) {
                $query->where('number', 'like', '%'.$q.'%');
            })
            ->orWhereHas('customer', function ($query) use ($q) {
                $query->where('account_no', 'like', '%'.$q.'%');
                $query->orWhere('name', 'like', '%'.$q.'%');
            })
            ->orderBy('date', 'desc')
            ->with('receipts', 'customer')
            ->paginate(25);
    }

    public function getCustomersInvoices($status)
    {
        return $this->model->where('network_id', auth()->user()->network_id)
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
            ->paginate(25);
    }

    public function getCustomerList()
    {
        $customers = Customer::where('customers.network_id', auth()->user()->network_id)
            ->whereHas('unInvoicedReceipts', function ($query) {
                return $query->whereNull('invoice_id');
            })
            ->with('invoiceReadyReceipts')
            ->get();

        return $customers;
    }

    public function createInvoiceFor(Customer $customer, $invoiceData)
    {
        $receipts = Receipt::whereIn('id', $invoiceData['receipt_id'])->get();
        $sumBillAmount = $receipts->sum('bill_amount');

        DB::beginTransaction();
        $invoice = $this->model;
        $invoice->number = $this->getNewInvoiceNumber();
        $invoice->type_id = 2;
        $invoice->periode = $invoiceData['periode'];
        $invoice->date = $invoiceData['date'];
        $invoice->end_date = $invoiceData['end_date'];
        $invoice->charge_details = $this->setChargeDetails($invoiceData);
        $invoice->amount = $sumBillAmount;
        $invoice->network_id = auth()->user()->network_id;
        $invoice->creator_id = auth()->id();
        $invoice->customer_id = $customer->id;
        $invoice->save();

        $invoice->assignReceipt($receipts);

        DB::commit();

        return $invoice;
    }

    private function setChargeDetails($invoiceData)
    {
        return [
            'discount'  => $invoiceData['discount'],
            'admin_fee' => $invoiceData['admin_fee'],
        ];
    }

    protected function getNewInvoiceNumber()
    {
        // format nomor : aaaa bb cc xxxx
        // aaaa : nomor/kode cabang (4 digit)
        // bb : tahun
        // cc : bulan
        // xxxx : nomor urut (Nomor urut direset setiap awal tahun)
        // Contoh : 630017020010
        $networkCode = auth()->user()->network->code;
        $networkCode = substr($networkCode, 0, 4);
        $yearMonth = date('ym');

        $lastInvoice = $this->model->where('number', 'like', $networkCode.$yearMonth.'%')->latest()->first();

        // @TODO reset counter every new year
        if (!is_null($lastInvoice)) {
            $currentNumber = substr($lastInvoice->number, -4);
            $currentNumber = $networkCode.$yearMonth.$currentNumber;
            return ++$currentNumber;
        }

        return $networkCode.$yearMonth.'0001';
    }

    public function getAccountingUsersList($networkId)
    {
        return User::whereIn('role_id', ['1', '2'])
            ->where('is_active', 1)
            ->where('network_id', $networkId)
            ->pluck('name', 'id');
    }
}
