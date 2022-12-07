@extends('layouts.customer-detail')

@section('subtitle', $title)

@section('show-links')
@if (Route::currentRouteName() == 'customers.un-invoiced-receipts' && $customer->unInvoicedReceipts()->count())
{!! link_to_route('invoices.create', trans('invoice.create'), [$customer->id], ['class' => 'btn btn-success']) !!}
@endif
@endsection

@section('customer-content')
@include('invoices.partials.receipt-lists', ['receiptPicker' => false])
{!! str_replace('/?', '?', $receipts->appends(Request::except('page'))->render()) !!}
@endsection