@extends('layouts.app')

@section('title', trans('cod_invoice.cod_invoice') . ' ' . $invoice->number)

@section('content')
<div class="pull-right hidden-print">
    @include('invoices.partials.cod-show-links')
</div>

<h2 class="page-header">
    {{ $invoice->number }} <small>{{ trans('cod_invoice.show') }}</small>
</h2>

@include('invoices.partials.cod-invoice-stat')

@if ($invoice->notes)
    <p class="well well-sm"><strong>{{ trans('app.notes') }} :</strong> {{ $invoice->notes }}</p>
@endif

@include('invoices.partials.cod-show-nav-tabs')

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('invoice.payments') }}</h3></div>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th class="text-center">{{ trans('transaction.number') }}</th>
                        <th class="text-center">{{ trans('app.date') }}</th>
                        <th class="text-right">{{ trans('transaction.types.in') }}</th>
                        <th class="text-right">{{ trans('transaction.types.out') }}</th>
                        <th class="text-center">{{ trans('transaction.payment_method') }}</th>
                        <th>{{ trans('app.notes') }}</th>
                        @can('entry-payment', $invoice)
                        <th class="text-center">{{ trans('app.action') }}</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @php
                        $incomeTotal = 0;
                        $outcomeTotal = 0;
                    @endphp
                    @forelse($invoice->payments as $key => $transaction)
                    <tr>
                        <td class="text-center">{{ 1 + $key }}</td>
                        <td class="col-xs-2 text-center">{{ $transaction->number }}</td>
                        <td class="col-xs-2 text-center">{{ $transaction->date }}</td>
                        <td class="col-xs-2 text-right">{{ formatRp($in = ($transaction->in_out == 0) ? 0 : $transaction->amount) }}</td>
                        <td class="col-xs-2 text-right">{{ formatRp($out = ($transaction->in_out == 1) ? 0 : $transaction->amount) }}</td>
                        <td class="col-xs-2 text-center">{{ $transaction->paymentMethod->name }}</td>
                        <td>{{ $transaction->notes }}</td>
                        @can('entry-payment', $invoice)
                        <td class="col-xs-2 text-center">
                            {!! html_link_to_route(
                                'invoices.cod.payments.index',
                                '',
                                [$invoice, 'action' => 'edit_payment', 'id' => $transaction->id],
                                ['icon' => 'edit', 'id' => 'edit_payment_'.$transaction->id]
                            ) !!}
                        </td>
                        @endcan
                    </tr>
                    @php
                        $incomeTotal += $in;
                        $outcomeTotal += $out;
                    @endphp
                    @empty
                    <tr><td colspan="5">{{ trans('invoice.empty_payment') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">{{ trans('app.total') }}</th>
                        <th class="text-right">{{ formatRp($incomeTotal) }}</th>
                        <th class="text-right">{{ formatRp($outcomeTotal) }}</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        @can('entry-payment', $invoice)
            @if(request('action') == 'edit_payment' && $editableTransaction)
            <div class="panel panel-warning">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('transaction.edit') }} - {{ $editableTransaction->number }}</h3></div>
                {!! Form::model($editableTransaction, [
                    'route' => ['invoices.cod.payments.update', $invoice, $editableTransaction],
                    'method' => 'patch',
                ]) !!}
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            {!! FormField::text('date', [
                                'label' => trans('transaction.date'),
                                'class' => 'date-select',
                                'required' => true
                            ]) !!}
                        </div>
                        <div class="col-md-6">
                            {!! FormField::price('amount', [
                                'label' => trans('invoice.payment_amount'),
                                'required' => true
                            ]) !!}
                        </div>
                    </div>
                    {!! FormField::radios('payment_method_id', $paymentMethods, ['label' => trans('invoice.payment_method'), 'required' => true]) !!}
                    {!! FormField::textarea('notes', ['label' => trans('app.notes'), 'required' => true]) !!}
                </div>
                <div class="panel-footer">
                    {!! Form::submit(trans('transaction.update'), ['class'=>'btn btn-warning']) !!}
                    {{ link_to_route('invoices.cod.show', trans('app.cancel'), $invoice, ['class' => 'btn btn-default pull-right']) }}
                </div>
                {!! Form::close() !!}
            </div>
            @else
            <div class="panel panel-success">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('invoice.outcome_entry') }}</h3></div>
                {!! Form::open([
                    'route'=> ['invoices.cod.payments.store', $invoice]
                ]) !!}
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            {!! FormField::text('date', [
                                'label' => trans('transaction.date'),
                                'class' => 'date-select',
                                'required' => true
                            ]) !!}
                        </div>
                        <div class="col-md-6">
                            {!! FormField::price('amount', [
                                'label' => trans('invoice.payment_amount'),
                                'required' => true
                            ]) !!}
                        </div>
                    </div>
                    {!! FormField::radios('payment_method_id', $paymentMethods, ['label' => trans('invoice.payment_method'), 'required' => true]) !!}
                    {!! FormField::textarea('notes', ['label' => trans('app.notes'), 'required' => true]) !!}
                </div>
                <div class="panel-footer">
                    {{ Form::submit(trans('invoice.outcome_entry'), ['class' => 'btn btn-success']) }}
                </div>
                {!! Form::close() !!}
            </div>
            @endif
        @endcan
    </div>
</div>


@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('.date-select').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true
    });
})();
</script>
@endsection
