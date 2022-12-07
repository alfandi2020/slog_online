@extends('layouts.app')

@section('title', trans('cash_invoice.cash_invoice') . ' ' . $invoice->number)

@section('content')
<div class="pull-right hidden-print">
    @include('invoices.partials.cash-show-links')
</div>

<h2 class="page-header">
    {{ $invoice->number }} <small>{{ trans('cash_invoice.show') }}</small>
</h2>

@include('invoices.partials.cash-invoice-stat')

@if ($invoice->notes)
    <p class="well well-sm"><strong>{{ trans('app.notes') }} :</strong> {{ $invoice->notes }}</p>
@endif

@if (request('action') == 'add_remove_receipt')
    @can ('add-remove-receipt-of', $invoice)
        @include('invoices.partials.form-add-remove-receipts', [
            'assignRoute' => 'invoices.cash.assign-receipt',
            'removeRoute' => 'invoices.cash.remove-receipt',
            'manifestId' => $invoice->id,
            'doneRoute' => 'invoices.cash.show',
        ])
    @endcan
@endif
@include('invoices.partials.cash-receipt-lists', ['receipts' => $invoice->receipts, 'receiptPicker' => false])

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