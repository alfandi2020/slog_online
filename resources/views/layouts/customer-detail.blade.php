@extends('layouts.app')

@section('title')
{{ $customer->present()->numberName }} | @yield('subtitle', trans('customer.show'))
@endsection

@section('content')

<div class="pull-right">
    @yield('show-links')
    {!! link_to_route('customers.index', trans('customer.back_to_index'), [], ['class' => 'btn btn-default']) !!}
</div>
<br>

@include('customers.partials.nav-tabs')

@yield('customer-content')

@endsection