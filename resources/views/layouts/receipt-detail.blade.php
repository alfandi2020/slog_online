@extends('layouts.app')

@section('title')
{{ trans('receipt.show_title', ['number' => $receipt->number]) }} |
@yield('subtitle', trans('receipt.show'))
@endsection

@section('content')

@include('receipts.partials.receipt-stat')

<div class="pull-right">@yield('show-links')</div>

@include('receipts.partials.show-nav-tabs')

@yield('receipt-content')

@endsection