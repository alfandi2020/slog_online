@extends('layouts.receipt-detail')

@section('subtitle', trans('receipt.couriers'))

@section('receipt-content')
<div class="row">
    <div class="col-sm-4">
        @include('receipts.partials.receipt-data')
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.pickup_courier') }}</h3></div>
            @if ($receipt->pickupCourier)
            <table class="table">
                <tbody>
                    <tr><th class="col-xs-4">{{ trans('app.name') }}</th><td>{{ $receipt->pickupCourier->nameLink() }}</td></tr>
                    <tr><th>{{ trans('user.phone') }}</th><td>{{ $receipt->pickupCourier->phone }}</td></tr>
                    <tr><th>{{ trans('user.email') }}</th><td>{{ $receipt->pickupCourier->email }}</td></tr>
                    <tr><th>{{ trans('user.network') }}</th><td>{{ $receipt->pickupCourier->network->nameLink() }}</td></tr>
                    <tr><th>{{ trans('network.origin') }}</th><td>{!! $receipt->pickupCourier->network->fullOriginName('list') !!}</td></tr>
                </tbody>
            </table>
            @else
            <div class="panel-body text-danger">
                {{ trans('receipt.no_pickup_courier') }}
            </div>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.delivery_courier') }}</h3></div>
            @if ($receipt->deliveryCourier)
            <table class="table">
                <tbody>
                    <tr><th class="col-xs-4">{{ trans('app.name') }}</th><td>{{ $receipt->deliveryCourier->nameLink() }}</td></tr>
                    <tr><th>{{ trans('user.phone') }}</th><td>{{ $receipt->deliveryCourier->phone }}</td></tr>
                    <tr><th>{{ trans('user.email') }}</th><td>{{ $receipt->deliveryCourier->email }}</td></tr>
                    <tr><th>{{ trans('user.network') }}</th><td>{{ $receipt->deliveryCourier->network->nameLink() }}</td></tr>
                    <tr><th>{{ trans('network.origin') }}</th><td>{!! $receipt->deliveryCourier->network->fullOriginName('list') !!}</td></tr>
                </tbody>
            </table>
            @elseif ($receipt->hasStatusOf(['od']) && $receipt->distributionManifest())
            @php
                $deliveryCourier = $receipt->distributionManifest()->handler;
            @endphp
            <table class="table">
                <tbody>
                    <tr><th class="col-xs-4">{{ trans('app.name') }}</th><td>{{ $deliveryCourier->nameLink() }}</td></tr>
                    <tr><th>{{ trans('user.phone') }}</th><td>{{ $deliveryCourier->phone }}</td></tr>
                    <tr><th>{{ trans('user.email') }}</th><td>{{ $deliveryCourier->email }}</td></tr>
                    <tr><th>{{ trans('user.network') }}</th><td>{{ $deliveryCourier->network->nameLink() }}</td></tr>
                    <tr><th>{{ trans('network.origin') }}</th><td>{!! $deliveryCourier->network->fullOriginName('list') !!}</td></tr>
                </tbody>
            </table>
            @else
            <div class="panel-body text-danger">
                {{ trans('receipt.no_delivery_courier') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
