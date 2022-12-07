@extends('layouts.customer-detail')

@section('subtitle', trans('customer.invoice_list'))

@section('customer-content')

@include('customers.partials.invoices-nav-tabs')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            {{ trans('customer.invoice_list_title', ['status' => request('status'), 'count' => $invoices->total()]) }}
        </h3>
    </div>
    <div class="panel panel-default table-responsive">
        <table class="table table-condensed">
            <thead>
                <th>{{ trans('app.table_no') }}</th>
                <th class="col-md-1">{{ trans('invoice.number') }}</th>
                <th class="text-center">{{ trans('invoice.periode') }}</th>
                <th class="text-center">{{ trans('app.date') }}</th>
                <th class="text-center">{{ trans('invoice.end_date') }}</th>
                <th class="text-center">{{ trans('receipt.receipt') }}</th>
                <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
                <th class="text-center">{{ trans('app.status') }}</th>
                <th>{{ trans('app.action') }}</th>
            </thead>
            <tbody>
                <?php
                // $sumCount = 0;
                // $sumAmount = 0;
                ?>
                @forelse($invoices as $key => $invoice)
                <tr>
                    <td>{{ $key + $invoices->firstItem() }}</td>
                    <td>{{ $invoice->number }}</td>
                    <td class="text-center">{{ $invoice->periode }}</td>
                    <td class="text-center">{{ $invoice->date }}</td>
                    <td class="text-center">{{ $invoice->end_date }}</td>
                    <td class="text-center">{{ $count = $invoice->receipts->count() }}</td>
                    <td class="text-right">{{ formatRp($amount = $invoice->receipts->sum('bill_amount')) }}</td>
                    <td class="text-center">{!! $invoice->present()->statusLabel !!}</td>
                    <td>
                        {!! link_to_route('invoices.show', 'Detail',[$invoice->id],['class'=>'btn btn-info btn-xs']) !!}
                    </td>
                </tr>
                <?php
                // $sumCount += $count;
                // $sumAmount += $amount;
                ?>
                @empty
                <tr>
                    <td colspan="9">{{ trans('invoice.empty') }}</td>
                </tr>
                @endforelse
            </tbody>
            <?php /*
            <tfoot>
                <th colspan="6" class="">{{ trans('app.total') }}</th>
                <th class="text-center">{{ $sumCount }}</th>
                <th>&nbsp;</th>
                <th class="text-right">{{ formatRp($sumAmount) }}</th>
                <th>&nbsp;</th>
            </tfoot>
            */ ?>
        </table>
    </div>
</div>
{!! str_replace('/?', '?', $invoices->appends(Request::except('page'))->render()) !!}
@endsection
