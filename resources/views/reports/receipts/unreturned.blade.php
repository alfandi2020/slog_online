@php
$date = Request::get('date', date('Y-m-d'));
@endphp
@extends('layouts.app')

@section('title', trans('report.receipt.unreturned'))

@section('content')


<!-- Nav tabs -->
<ul class="nav nav-tabs underlined-tabs">
    <li class="{{ (Request::has('older') == false) ? 'active' : '' }}">
        {!! html_link_to_route('reports.receipt.unreturned', trans('report.receipt.last_quartal'), [], ['icon' => 'file']) !!}
    </li>
    <li class="{{ (Request::get('older') == 1) ? 'active' : '' }}">
        {!! html_link_to_route('reports.receipt.unreturned', trans('report.receipt.before_last_quartal'), ['older' => 1], ['icon' => 'file-archive-o']) !!}
    </li>
</ul>

<br>
<p>Laporan Resi yang <strong>belum dikembalikan</strong> ke Cabang Pengirim</p>

@foreach($datedReceipts as $date => $receipts)

<div class="panel panel-default table-responsive">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('app.date') }} : {{ $date }}</h3></div>
    <table class="table table-condensed table-hover">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th class="col-sm-1 text-center">{{ trans('receipt.number') }}</th>
            <th class="col-sm-2">{{ trans('receipt.customer') }}</th>
            <th class="col-sm-2 text-center">{{ trans('receipt.destination') }}</th>
            <th class="col-sm-1 text-center">{{ trans('service.service') }}</th>
            <th class="col-sm-1 text-center">{{ trans('receipt.items_count') }}</th>
            <th class="col-sm-1 text-center">{{ trans('receipt.weight') }}</th>
            <th class="col-sm-2 text-right">{{ trans('receipt.amount') }}</th>
            <th class="col-sm-1 text-center">{{ trans('receipt.payment') }}</th>
            <th class="col-sm-2 text-center">{{ trans('app.status') }}</th>
        </thead>
        <tbody>
            @forelse($receipts as $key => $receipt)
            <tr>
            <td>{{ $key + 1 }}</td>
                <td class="text-center">
                    {!! link_to_route('receipts.show', $receipt->number, [$receipt->number], ['title' => 'Lihat Detail ' . $receipt->number]) !!}
                </td>
                <td>{{ $receipt->customer ? $receipt->customer->name : '' }}</td>
                <td class="text-center">{{ $receipt->destinationName() }}</td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td class="text-center">{{ $receipt->items_count }}</td>
                <td class="text-center">{{ $receipt->weight }}</td>
                <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
                <td class="text-center">{{ $receipt->present()->paymentType() }}</td>
                <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10">{{ trans('receipt.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach

@endsection