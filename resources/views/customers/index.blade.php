@extends('layouts.app')

@section('title', __('customer.list').' '.__('app.total').' : '.$customers->total())

@section('content')
<div class="well well-sm">
    {!! Form::open(['method' => 'get', 'class' => 'form-inline']) !!}
    {!! Form::text('q', Request::get('q'), ['class' => 'form-control', 'placeholder' =>__('customer.search'), 'style' => 'width:250px']) !!}
    {!! Form::select('category_id', config('bam-cargo.customer_categories'), Request::get('category_id'), ['class' => 'form-control', 'placeholder' => '-- Semua Kategori --']) !!}
    {!! Form::submit(__('customer.search'), ['class' => 'btn btn-info']) !!}
    {!! link_to_route('customers.index', 'Reset',[],['class' => 'btn btn-default']) !!}
    {!! link_to_route('customers.create', __('customer.create'), [], ['class' => 'btn btn-success']) !!}
    {!! link_to_route('customers.export', __('customer.list_export'), [], ['class' => 'btn btn-default pull-right']) !!}
    {!! Form::close() !!}
</div>

<div class="panel panel-default table-responsive">
    <div class="panel-body">
    <table class="table table-condensed">
        <thead>
            <th class="text-center">{{ __('app.table_no') }}</th>
            <th>{{ __('customer.customer') }}</th>
            <th class="text-center">{{ __('app.code') }}</th>
            <th class="text-center">{{ __('customer.category') }}</th>
            <th>{{ __('network.network') }}</th>
            <th class="text-right">{{ __('receipt.un_invoiced') }}</th>
            <th class="text-center">{{ __('comodity.comodity') }}</th>
            <th class="text-center">{{ __('customer.rates_count') }}</th>
            <th class="text-center">{{ __('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($customers as $key => $customer)
            <tr>
                <td class="text-center">{{ $customers->firstItem() + $key }}</td>
                <td>{{ $customer->present()->numberNameLink }}</td>
                <td class="text-center">{{ $customer->code }}</td>
                <td class="text-center">{{ $customer->category }}</td>
                <td>{{ $customer->network->name }}</td>
                <td class="text-right">
                    @if ($customer->receipts_count)
                    {{ link_to_route('customers.un-invoiced-receipts',
                        '('.$customer->receipts_count.' '.__('receipt.receipt').') '
                       .formatRp($uninvoiced = $customer->bill_sum), [
                        $customer->id
                        ], [
                        'title' => __('receipt.un_invoiced').' : '.formatRp($uninvoiced),
                        'style' => 'text-decoration:none'
                    ]) }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-center">{{ $customer->comodity->name }}</td>
                <td class="text-center">
                    @if ($customer->rates_count)
                    {{ link_to_route('customers.rates.index', $customer->rates_count, [$customer->id], ['title' => 'Lihat '.__('customer.rates')]) }}
                    @endif
                </td>
                <td class="text-center">
                    @if ($customer->receipts_count)
                        {{ link_to_route('invoices.create',
                            __('invoice.create'), [$customer->id ], [
                            'class' => 'btn btn-success btn-xs'
                        ]) }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">{{ __('customer.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {!! str_replace('/?', '?', $customers->appends(Request::except('page'))->render()) !!}
    </div>
</div>
@endsection
