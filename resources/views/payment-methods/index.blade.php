@extends('layouts.app')

@section('title', trans('payment_method.list'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default table-responsive">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('payment_method.name') }}</th>
                        <th>{{ trans('app.description') }}</th>
                        <th>{{ trans('app.active') }}</th>
                        <th class="text-center">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentMethods as $key => $paymentMethod)
                    <tr>
                        <td class="text-center">{{ 1 + $key }}</td>
                        <td>{{ $paymentMethod->name }}</td>
                        <td>{!! nl2br($paymentMethod->description) !!}</td>
                        <td>{{ $paymentMethod->is_active }}</td>
                        <td class="text-center">
                            {!! link_to_route(
                                'payment-methods.index',
                                trans('app.edit'),
                                ['action' => 'edit', 'id' => $paymentMethod->id] + Request::only('page', 'q'),
                                ['id' => 'edit-payment_method-' . $paymentMethod->id]
                            ) !!} |
                            {!! link_to_route(
                                'payment-methods.index',
                                trans('app.delete'),
                                ['action' => 'delete', 'id' => $paymentMethod->id] + Request::only('page', 'q'),
                                ['id' => 'del-payment_method-' . $paymentMethod->id]
                            ) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        @if (request('action') == null)
        <div class="pull-right">
            {{ link_to_route('payment-methods.index', trans('payment_method.create'), ['action' => 'create'], ['class' => 'btn btn-success']) }}
        </div>
        @endif
        @includeWhen(Request::has('action'), 'payment-methods.forms')
    </div>
</div>
@endsection
