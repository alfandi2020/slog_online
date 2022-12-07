@extends('layouts.app')

@section('title', trans('report.invoice'))

@section('content')

<div class="pull-right">
    {!! html_link_to_route(
        'reports.invoices',
        trans('report.export_invoice_list'),
        request(['status']) + ['export' => 'xls'],
        ['class' => 'btn btn-default', 'icon' => 'file-excel-o', 'style' => 'margin: -7px 0px;']
    ) !!}
</div>

@include('reports.partials.invoices-nav-tabs')

<div class="panel panel-default table-responsive">
    <div class="panel-heading">
        <h3 class="panel-title">
            {{ trans('customer.invoice_list_title', ['status' => request('status'), 'count' => $invoices->total()]) }}
        </h3>
    </div>
    <table class="table table-condensed">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('invoice.number') }}</th>
            <th>{{ trans('invoice.customer') }}</th>
            <th class="text-center">{{ trans('invoice.periode') }}</th>
            <th class="text-center">{{ trans('app.date') }}</th>
            <th class="text-center">{{ trans('invoice.end_date') }}</th>
            <th class="text-center">{{ trans('receipt.receipt') }}</th>
            <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
            <th class="text-center">{{ trans('app.status') }}</th>
        </thead>
        <tbody>
            <?php
                $sumCount = 0;
                $sumAmount = 0;
            ?>
            @forelse($invoices as $key => $invoice)
            <tr>
                <td>{{ $key + $invoices->firstItem() }}</td>
                <td>{{ $invoice->present()->numberLink }}</td>
                <td>{{ $invoice->customer->present()->numberName }}</td>
                <td class="text-center">{{ $invoice->periode }}</td>
                <td class="text-center">{{ $invoice->date }}</td>
                <td class="text-center">{{ $invoice->end_date }}</td>
                <td class="text-center">{{ $count = $invoice->receipts->count() }}</td>
                <td class="text-right">{{ formatRp($amount = $invoice->receipts->sum('bill_amount')) }}</td>
                <td class="text-center">{!! $invoice->present()->statusLabel !!}</td>
            </tr>
            <?php
                $sumCount += $count;
                $sumAmount += $amount;
            ?>
            @empty
            <tr>
                <td colspan="9">{{ trans('invoice.empty') }}</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <th colspan="6" class="text-center">{{ trans('app.total') }}</th>
            <th class="text-center">{{ $sumCount }}</th>
            <th class="text-right">{{ formatRp($sumAmount) }}</th>
            <th>&nbsp;</th>
        </tfoot>
    </table>
</div>
{!! str_replace('/?', '?', $invoices->appends(Request::except('page'))->render()) !!}
@endsection
