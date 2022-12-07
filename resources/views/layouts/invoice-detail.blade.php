@extends('layouts.app')

@section('title')
{{ trans('invoice.number') . ' ' . $invoice->number }} |
@yield('subtitle', trans('invoice.show'))
@endsection

@section('content')

<div class="pull-right">
    @include('invoices.partials.show-links')
</div>
<h2 class="page-header">
    {{ trans('invoice.number') }} {{ $invoice->number }} <small>@yield('subtitle', trans('invoice.show'))</small>
</h2>

@include('invoices.partials.show-nav-tabs')

@yield('content-invoice')

@endsection
