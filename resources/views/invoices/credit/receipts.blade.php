@extends('layouts.invoice-detail')

@section('subtitle', trans('invoice.receipts'))

@section('content-invoice')

@can ('add-remove-receipt-of', $invoice)
    @include('invoices.partials.form-add-remove-receipts', [
        'assignRoute' => 'invoices.assign-receipt',
        'removeRoute' => 'invoices.remove-receipt',
        'manifestId' => $invoice->id,
    ])
@endcan

@include('invoices.partials.receipt-lists', ['receipts' => $invoice->receipts, 'receiptPicker' => false])

@endsection
