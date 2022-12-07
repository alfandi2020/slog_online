@inject('paymentType','App\Entities\Receipts\PaymentType')
@inject('dashboardQuery','App\Entities\Reports\AdminDashboardQuery')
@inject('network','App\Entities\Networks\Network')
@extends('layouts.app')

@section('title', 'Resi '.trans('report.monitor.'.request()->segment(2)))

@section('content')

<div class="panel panel-default hidden-xs">
    <table class="table table-condensed table-bordered">
        <tr>
            <td class="col-xs-2 text-center">{{ trans('app.total') }}</td>
            <td class="col-xs-2 text-center">{{ trans('manifest.receipts_count') }}</td>
            <td class="col-xs-2 text-center">{{ trans('report.year_month') }}</td>
            @if (request('network_id'))
                <td class="col-xs-2 text-center">{{ trans('network.network') }}</td>
            @endif
            <td class="col-xs-2 text-center">{{ trans('receipt.payment_type') }}</td>
        </tr>
        <tr>
            <td class="text-center lead" style="border-top: none;">{{ formatRp($receipts->sum('bill_amount')) }}</td>
            <td class="text-center lead" style="border-top: none;">{{ $receipts->count() }}</td>
            <td class="text-center lead" style="border-top: none;">{{ request('ym') }}</td>
            @if (request('network_id'))
                <td class="text-center lead" style="border-top: none;">{{ $network::findOrFail(request('network_id'))->name }}</td>
            @endif
            <td class="text-center lead" style="border-top: none;">{{ $paymentType::getNameById(request('payment_type_id')) ?: 'Semua' }}</td>
        </tr>
    </table>
</div>

<ul class="list-group visible-xs">
    <li class="list-group-item">{{ trans('app.total') }} <span class="pull-right">{{ formatRp($receipts->sum('bill_amount')) }}</span></li>
    <li class="list-group-item">{{ trans('manifest.receipts_count') }} <span class="pull-right">{{ $receipts->count() }}</span></li>
    <li class="list-group-item">{{ trans('report.year_month') }} <span class="pull-right">{{ request('ym') }}</span></li>
    @if (request('network_id'))
        <li class="list-group-item">{{ trans('network.network') }} <span class="pull-right">{{ $network::findOrFail(request('network_id'))->name }}</span></li>
    @endif
    <li class="list-group-item">{{ trans('receipt.payment_type') }} <span class="pull-right">{{ $paymentType::getNameById(request('payment_type_id')) ?: 'Semua' }}</span></li>
</ul>

<div class="panel panel-default table-responsive">
<table class="table table-condensed small-text">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th class="text-center">{{ trans('app.date') }}</th>
        <th class="text-center">{{ trans('receipt.number') }}</th>
        <th style="width: 150px;">{{ trans('receipt.customer') }}</th>
        <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
        <th class="text-center">{{ trans('service.service') }}</th>
        <th class="text-center">{{ trans('receipt.items_count') }}</th>
        <th class="text-right">{{ trans('receipt.weight') }}</th>
        <th class="text-center">{{ trans('app.status') }}</th>
        <th>{{ trans('receipt.destination') }}</th>
        <th>{{ trans('receipt.location') }}</th>
        <th>{{ trans('receipt.officer') }}</th>
    </thead>
    <tbody>
        @forelse($receipts as $key => $receipt)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td class="text-center">{{ $receipt->pickup_time->format('Y-m-d') }}</td>
            <td class="text-center">{{ $receipt->numberLink() }}</td>
            <td>{{ $receipt->present()->customerName }}</td>
            {{-- <td class="text-right">{{ formatRp($receipt->amount) }}</td> --}}
            <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
            <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
            <td class="text-center">{{ $receipt->items_count }}</td>
            <td class="text-right">{{ $receipt->weight }}</td>
            <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
            <td>
                @if ($receipt->destDistrictName())
                    {{ $receipt->destDistrictName() }}
                @else
                    {{ $receipt->destCityName() }}
                @endif
            </td>
            <td>{{ $receipt->lastLocation() }}</td>
            <td>{{ $receipt->lastOfficer() }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9">{{ trans('receipt.no_receipts') }}</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th class="text-right" colspan="2">{{ trans('app.count') }}</th>
            <th>{{ $receipts->count() }} {{ trans('receipt.receipt') }}</th>
            <td>&nbsp;</td>
            {{-- <th class="text-right">{{ formatRp($receipts->sum('amount')) }}</th> --}}
            <th class="text-right">{{ formatRp($receipts->sum('bill_amount')) }}</th>
            <td>&nbsp;</td>
            <th class="text-center">{{ $receipts->sum('items_count') }}</th>
            <th class="text-right">{{ $receipts->sum('weight') }}</th>
            <td colspan="4">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</div>
@endsection
