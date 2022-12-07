@extends('layouts.app')

@section('title', trans('customer.delete'))

@section('content')
<h2 class="page-header">
    <div class="pull-right">
        {!! FormField::delete(['route'=>['customers.destroy',$customer->id]], trans('customer.delete'), [
            'class'=>'btn btn-danger'
        ], ['customer_id' => $customer->id]) !!}
    </div>
    {{ trans('app.delete_confirm') }}
    {!! link_to_route('customers.edit', trans('app.cancel'), [$customer->id], ['class' => 'btn btn-default']) !!}
</h2>

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('customer.show') }}</h3></div>
            <table class="table table-condensed">
                <tbody>
                    <tr><th>{{ trans('customer.account_no') }}</th><td>{{ $customer->account_no }}</td></tr>
                    <tr><th>{{ trans('app.code') }}</th><td>{{ $customer->code }}</td></tr>
                    <tr><th>{{ trans('customer.name') }}</th><td>{{ $customer->name }}</td></tr>
                    <tr><th>{{ trans('customer.npwp') }}</th><td>{{ $customer->npwp }}</td></tr>
                    <tr><th>{{ trans('customer.start_date') }}</th><td>{{ $customer->start_date }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection