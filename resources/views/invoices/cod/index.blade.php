@extends('layouts.app')

@section('title', trans('cod_invoice.list'))

@section('content')
<div class="pull-right">
    @can('create', new App\Entities\Invoices\Cod)
    {{ link_to_route('invoices.cod.create', trans('cod_invoice.create'), [], ['class' => 'btn btn-success btn-sm','id' => 'create_cod_invoice']) }}
    @endif
</div>

@include('invoices.partials.cod-index-nav-tabs')

<div class="panel panel-default table-responsive">
    <div class="panel-heading">
        <h3 class="panel-title">
            {{ trans('cod_invoice.list_title', ['status' => request('status'), 'count' => $invoices->total()]) }}
        </h3>
    </div>
    <table class="table table-condensed">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('cod_invoice.number') }}</th>
            <th class="text-center">{{ trans('app.date') }}</th>
            <th class="text-center">{{ trans('receipt.receipt') }}</th>
            <th class="text-right">{{ trans('invoice.total') }}</th>
            <th>{{ trans('invoice.creator') }}</th>
            <th>{{ trans('invoice.handler') }}</th>
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
                <td class="text-center">{{ $invoice->date }}</td>
                <td class="text-center">{{ $count = $invoice->receipts->count() }}</td>
                <td class="text-right">{{ formatRp($amount = $invoice->receipts->sum('bill_amount')) }}</td>
                <td>{{ $invoice->creator->name }}</td>
                <td>{{ $invoice->handler_id ? $invoice->handler->name : '' }}</td>
                <td class="text-center">{!! $invoice->present()->statusLabel !!}</td>
                <td>
                    {!! link_to_route('invoices.cod.show', 'Detail',[$invoice->id],['class'=>'btn btn-info btn-xs']) !!}
                </td>
            </tr>
            <?php
            // $sumCount += $count;
            // $sumAmount += $amount;
            ?>
            @empty
            <tr>
                <td colspan="9">{{ trans('cod_invoice.empty') }}</td>
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
{!! str_replace('/?', '?', $invoices->appends(Request::except('page'))->render()) !!}
@endsection
