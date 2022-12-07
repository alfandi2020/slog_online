@extends('layouts.app')

@section('title', trans('invoice.search'))

@section('content')

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
{!! Form::label('q', trans('invoice.search'), ['class'=>'control-label']) !!}
{!! Form::text('q', Request::get('q'), [
    'class'=>'form-control', 'required', 'style' => 'width:320px',
    'placeholder' => trans('invoice.search_placeholder')
]) !!}
{!! Form::submit(trans('invoice.search'), ['class'=>'btn btn-info']) !!}
{!! link_to_route('invoices.search', trans('app.reset'), [], ['class' => 'btn btn-default']) !!}
{!! Form::close() !!}

@if (empty($invoices))
<div class="alert alert-info">
    {!! trans('invoice.search_alert_info') !!}
</div>
@else
<div class="panel panel-default table-responsive">
    <div class="panel-heading">
        <h3 class="panel-title">
            {{ trans('invoice.found') }} : {{ $invoices->total() }}
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
            <th>{{ trans('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($invoices as $key => $invoice)
            <tr>
                <td>{{ $key + $invoices->firstItem() }}</td>
                <td>{{ $invoice->number }}</td>
                <td>{{ $invoice->customer_id ? $invoice->customer->present()->numberName : '-' }}</td>
                <td class="text-center">{{ $invoice->periode }}</td>
                <td class="text-center">{{ $invoice->date }}</td>
                <td class="text-center">{{ $invoice->end_date }}</td>
                <td class="text-center">{{ $count = $invoice->receipts->count() }}</td>
                <td class="text-right">{{ formatRp($amount = $invoice->receipts->sum('bill_amount')) }}</td>
                <td class="text-center">{!! $invoice->present()->statusLabel !!}</td>
                <td>
                    <?php
                    $routeLink = 'invoices.show';

                    if ($invoice->type_id == 1) {
                        $routeLink = 'invoices.cash.show';
                    } else if ($invoice->type_id == 3) {
                        $routeLink = 'invoices.cod.show';
                    }
                    ?>
                    {!! link_to_route($routeLink, 'Detail',[$invoice->id],['class'=>'btn btn-info btn-xs']) !!}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">{{ trans('invoice.empty') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
{!! str_replace('/?', '?', $invoices->appends(Request::except('page'))->render()) !!}
@endif
@endsection
