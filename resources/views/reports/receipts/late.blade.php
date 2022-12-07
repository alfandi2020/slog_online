@php
$date = Request::get('date', date('Y-m-d'));
@endphp
@extends('layouts.app')

@section('title', trans('report.receipt.late'))

@section('content')

<p>Laporan Pengiriman Paket/Resi yang <b>belum terkirim ke penerima</b> selama <b>lebih dari 7 hari</b> sejak entry ke sistem.</p>

<div class="panel panel-default table-responsive">
    <table class="table table-condensed table-hover">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('receipt.number') }}</th>
            <th class="text-center">{{ trans('service.service') }}</th>
            <th class="">{{ trans('receipt.customer') }}</th>
            <th class="">{{ trans('receipt.destination') }}</th>
            <th class="text-center">{{ trans('app.status') }}</th>
            <th class="">{{ trans('receipt.location') }}</th>
            <th>{{ trans('app.last_update') }}</th>
            <th>{{ trans('receipt.officer') }}</th>
        </thead>
        <tbody>
            @forelse($receipts as $key => $receipt)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">
                    {!! link_to_route('receipts.show', $receipt->number, [$receipt->number], ['title' => 'Lihat Detail ' . $receipt->number]) !!}
                </td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td>{{ $receipt->customer ? $receipt->customer->name : '' }}</td>
                <td>{{ $receipt->destinationName() }}</td>
                <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
                <td>{{ $receipt->lastLocation() }}</td>
                <td>{{ Date::parse($receipt->updated_at)->diffForHumans() }}</td>
                <td>{{ $receipt->lastOfficer() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10">{{ trans('receipt.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection