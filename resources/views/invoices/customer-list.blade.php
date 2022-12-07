@extends('layouts.app')

@section('title', trans('customer.un_invoiced'))

@section('content')

<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th>{{ trans('customer.name') }}</th>
            <th class="text-right">{{ trans('customer.receipts_total') }}</th>
            <th class="text-center">{{ trans('customer.receipts_count') }}</th>
            <th>{{ trans('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($customers as $key => $customer)
            <tr>
                <td class="text-center">{{ 1 + $key }}</td>
                <td>{{ $customer->present()->numberNameLink }}</td>
                <td class="text-right">{{ formatRp($customer->invoiceReadyReceipts->sum('bill_amount')) }}</td>
                <td class="text-center">{{ $customer->invoiceReadyReceipts->count() }}</td>
                <td>
                    {{ link_to_route('invoices.create', trans('invoice.create'), [$customer->id], [
                        'id' => 'create-invoice-' . $customer->id,
                        'class' => 'btn btn-success btn-xs',
                        'title' => trans('invoice.create') . ' ' . $customer->name,
                    ]) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">{{ trans('customer.un_invoiced_empty') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
