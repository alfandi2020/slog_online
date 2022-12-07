<?php

namespace App\Http\Controllers\Customers;

use Excel;
use Illuminate\Http\Request;
use App\Entities\Networks\Network;
use App\Entities\Customers\Customer;
use App\Http\Controllers\Controller;
use App\Entities\References\Reference;
use App\Http\Requests\Customers\CreateRequest;
use App\Http\Requests\Customers\UpdateRequest;
use App\Entities\Customers\CustomersRepository;

class CustomersController extends Controller
{
    private $repo;

    public function __construct(CustomersRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $customers = $this->repo->getCustomersList($request->get('q'), $request->get('category_id'));

        return view('customers.index', compact('customers'));
    }
    public function create()
    {
        $comodities = Reference::comodity()->pluck('name', 'id');
        $networks = Network::whereIn('type_id', [1, 2])->pluck('name', 'id');

        return view('customers.create', compact('comodities', 'networks'));
    }

    public function store(CreateRequest $createForm)
    {
        $createForm->persist();

        flash(trans('customer.created'), 'success');
        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        $comodities = Reference::comodity()->pluck('name', 'id');
        $networks = Network::whereIn('type_id', [1, 2])->pluck('name', 'id');

        return view('customers.edit', compact('customer', 'comodities', 'networks'));
    }

    public function update(UpdateRequest $updateForm, Customer $customer)
    {
        $updateForm->persist();

        flash(trans('customer.updated'), 'success');
        return redirect()->route('customers.edit', $customer->id);
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function delete(Customer $customer)
    {
        return view('customers.delete', compact('customer'));
    }

    public function destroy(Request $request, Customer $customer)
    {
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id|not_exists:receipts,customer_id|not_exists:manifests,customer_id|not_exists:invoices,customer_id',
        ], [
            'customer_id.not_exists' => trans('customer.undeleted_reason'),
        ]);

        if ($request->get('customer_id') == $customer->id && $customer->delete()) {
            flash(trans('customer.deleted'), 'success');
            return redirect()->route('customers.index');
        }

        flash(trans('customer.undeleted'), 'error');
        return back();
    }

    public function invoices(Request $request, Customer $customer)
    {
        $invoices = $customer->invoices()->with('receipts')->paginate(25);

        if ($request->get('status') == 'sent') {
            $invoices = $customer->invoices()->where(function ($query) {
                return $query->isSent();
            })->with('receipts')->paginate(25);
        } elseif ($request->get('status') == 'paid') {
            $invoices = $customer->invoices()->where(function ($query) {
                return $query->isPaid();
            })->with('receipts')->paginate(25);
        } elseif ($request->get('status') == 'verified') {
            $invoices = $customer->invoices()->where(function ($query) {
                return $query->isVerified();
            })->with('receipts')->paginate(25);
        }

        return view('customers.invoices', ['customer' => $customer, 'invoices' => $invoices]);
    }

    public function invoicedReceipts(Customer $customer)
    {
        return view('customers.receipts', [
            'customer' => $customer,
            'receipts' => $customer->invoicedReceipts()->paginate(25),
            'title'    => trans('receipt.invoiced'),
        ]);
    }

    public function unInvoicedReceipts(Customer $customer)
    {
        return view('customers.receipts', [
            'customer' => $customer,
            'receipts' => $customer->unInvoicedReceipts()->paginate(25),
            'title'    => trans('receipt.un_invoiced'),
        ]);
    }

    public function export()
    {
        $customers = Customer::with('network', 'comodity')->get();
        // return view('customers.export-list', compact('customers'));

        Excel::create('Customers', function ($excel) use ($customers) {
            $excel->sheet('Customers', function ($sheet) use ($customers) {
                $sheet->loadView('customers.export-list', compact('customers'));
            });
        })->export('xls');
    }
}
