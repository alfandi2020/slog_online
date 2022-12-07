@inject('paymentType','App\Entities\Receipts\PaymentType')
@inject('dashboardQuery','App\Entities\Reports\AdminDashboardQuery')
@inject('network','App\Entities\Networks\Network')
@extends('layouts.app')

@section('title', 'Resi Beredar (Tunai)')

@section('content')

<div class="well well-sm">
    <h4 style="margin-top: 0px">
        {{ trans('app.total') }} : <strong>{{ formatRp($receipts->sum('bill_amount')) }}</strong>
        ({{ $receipts->count() }} {{ trans('receipt.receipt') }})
    </h4>
    <div>
        Bulan Tahun : <strong>{{ request('ym') }}</strong>,
        @if (request('network_id'))
        Kantor Cabang : <strong>{{ $network::findOrFail(request('network_id'))->name }}</strong>,
        @endif
        Jenis Pembayaran : <strong>{{ $paymentType::getNameById(1) }}</strong>
    </div>
</div>

<div class="panel panel-default table-responsive">
<table class="table table-condensed small-text">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th class="text-center">{{ trans('app.date') }}</th>
        <th class="text-center">{{ trans('receipt.number') }}</th>
        <th>{{ trans('receipt.customer') }}</th>
        <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
        <th class="text-center">{{ trans('service.service') }}</th>
        <th class="text-center">{{ trans('receipt.items_count') }}</th>
        <th class="text-right">{{ trans('receipt.weight') }}</th>
        <th class="text-center">{{ trans('app.status') }}</th>
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
            <td class="text-right">{{ formatRp($receipt->amount) }}</td>
            <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
            <td class="text-center">{{ $receipt->items_count }}</td>
            <td class="text-right">{{ $receipt->weight }}</td>
            <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
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
            <th class="text-right">{{ formatRp($receipts->sum('amount')) }}</th>
            <td>&nbsp;</td>
            <th class="text-center">{{ $receipts->sum('items_count') }}</th>
            <th class="text-right">{{ $receipts->sum('weight') }}</th>
            <td colspan="3">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</div>
@endsection