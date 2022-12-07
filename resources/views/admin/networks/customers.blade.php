@extends('layouts.app')

@section('title', trans('customer.list'))

@section('content')

<div class="pull-right">
    {!! link_to_route('customers.create', trans('customer.create'), ['network_id' => $network->id], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">
    {{ $network->name }} <small>{{ trans('customer.list') }}: {{ $network->customers->count() }}</small>
</h2>

@include('admin.networks.partials.nav-tabs')

<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('customer.account_no') }}</th>
            <th>{{ trans('customer.name') }}</th>
            <th>{{ trans('app.code') }}</th>
            <th>{{ trans('customer.pic') }}</th>
            <th>{{ trans('comodity.comodity') }}</th>
            <th>{{ trans('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($network->customers as $key => $customer)
            <tr>
                <td class="text-center">{{ 1 + $key }}</td>
                <td class="text-center">{{ $customer->account_no }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->code }}</td>
                <td>{{ $customer->pic['name'] }}</td>
                <td>{{ $customer->comodity->name }}</td>
                <td>
                    {!! html_link_to_route('customers.show', '', [$customer->id], ['icon' => 'search', 'title' => trans('customer.show')]) !!} |
                    {!! html_link_to_route('customers.edit', '', [$customer->id], ['icon' => 'edit', 'title' => trans('customer.edit')]) !!}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">{{ trans('customer.empty') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
