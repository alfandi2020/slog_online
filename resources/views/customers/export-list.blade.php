@extends('layouts.plain')

@section('title', trans('customer.list'))

@section('styles')
@endsection

@section('content')
<table>
    <thead>
        <tr><th colspan="2"><h3>Export List Customer</h3></th></tr>
        <tr><td>Per</td><td>{{ dateId(date('Y-m-d')) }}</td></tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('customer.account_no') }}</th>
            <th>{{ trans('customer.code') }}</th>
            <th>{{ trans('customer.customer') }}</th>
            <th>{{ trans('comodity.comodity') }}</th>
            <th>{{ trans('network.network') }}</th>
            <th>{{ trans('customer.npwp') }}</th>
            <th>{{ trans('customer.is_taxed') }}</th>
            <th>{{ trans('customer.is_active') }}</th>
            <th>{{ trans('customer.pic_name') }}</th>
            <th>{{ trans('customer.pic_phone') }}</th>
            <th>{{ trans('customer.pic_email') }}</th>
            <th>{{ trans('customer.start_date') }}</th>
            <th>{{ trans('address.address') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $key => $customer)
        <tr>
            <td>{{ 1 + $key }}</td>
            <td>{{ $customer->account_no }}</td>
            <td>{{ $customer->code }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->comodity->name }}</td>
            <td>{{ $customer->network->name }}</td>
            <td>{{ $customer->npwp }}</td>
            <td>{{ $customer->is_taxed ? 'Ya' : 'Tidak' }}</td>
            <td>{{ $customer->is_active ? 'Ya' : 'Tidak' }}</td>
            <td>{{ $customer->pic['name'] }}</td>
            <td>{{ $customer->pic['phone'] }}</td>
            <td>{{ $customer->pic['email'] }}</td>
            <td>{{ $customer->start_date }}</td>
            <td>{{ $customer->address[1] }}, {{ $customer->address[2] }}, {{ $customer->address[3] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
