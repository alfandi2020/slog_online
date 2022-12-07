@inject('paymentTypes','App\Entities\Receipts\PaymentType')
@inject('statuses','App\Entities\Receipts\Status')
@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', 'Export List Resi')

@section('content')
<?php
    // TODO: add report lang
?>

<p>Export list Resi sesuai filter <b>Tanggal</b>, <b>Jenis Pembayaran</b> dan <b>Status</b></p>

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
<div style="margin-bottom: 10px">
{!! FormField::text('start_date', ['label' => trans('app.from'),'value' => request('start_date', $receiptQuery['start_date']), 'style' => 'width:100px']) !!}
{!! FormField::text('end_date', ['label' => trans('app.to'),'value' => request('end_date', $receiptQuery['end_date']), 'style' => 'width:100px']) !!}
{!! FormField::select('payment_type_id', $paymentTypes::dropdown(), [
    'label' => false,
    'value' => request('payment_type_id', $receiptQuery['payment_type_id']),
    'placeholder' => 'Semua Pembayaran',
]) !!}
{!! FormField::select('status_code', $statuses::toArray(), [
    'label' => false,
    'value' => request('status_code', $receiptQuery['status_code']),
    'placeholder' => 'Pilih Status',
]) !!}
{!! FormField::select('customer_id', $customers, [
    'label' => false,
    'value' => request('customer_id', $receiptQuery['customer_id']),
    'placeholder' => 'Semua Customer',
    'id' => 'customer_id',
]) !!}
</div>
{!! FormField::select('dest_city_id', $cities, ['label' => trans('receipt.destination'), 'placeholder' => trans('address.city')]) !!}
{!! FormField::select('dest_district_id', $regionQuery->getDistrictsList(request('dest_city_id')), ['label' => false, 'placeholder' => 'Semua Kecamatan']) !!}
{{-- {!! FormField::select('courier_id', $couriers, [
    'label' => false,
    'value' => request('courier_id', $receiptQuery['courier_id']),
    'placeholder' => 'Semua Driver',
    'id' => 'courier_id',
]) !!} --}}
{!! FormField::select('network_id', $networks, [
    'label' => false,
    'value' => request('network_id', $receiptQuery['network_id']),
    'placeholder' => 'Semua Cabang',
    'id' => 'network_id',
]) !!}
{!! Form::submit(trans('app.filter'), ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.receipt.export', trans('app.reset'), [], ['class'=>'btn btn-default']) !!}
@if (! $receipts->isEmpty())
{!! link_to_route('reports.receipt.export', trans('app.export'), Request::only([
    'start_date', 'end_date',
    'payment_type_id', 'status_code',
    'customer_id', 'dest_city_id',
    'dest_district_id', 'courier_id',
    'network_id'
]) + ['export' => 1], ['class'=>'btn btn-default']) !!}
@endif
{!! Form::close() !!}

<div class="panel panel-default table-responsive">
<table class="table table-condensed">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th class="text-center">{{ trans('app.date') }}</th>
        <th>{{ trans('receipt.number') }}</th>
        <th class="text-right">{{ trans('receipt.amount') }}</th>
        <th class="text-center">{{ trans('service.service') }}</th>
        <th class="text-center">{{ trans('receipt.items_count') }}</th>
        <th class="text-right">{{ trans('receipt.weight') }}</th>
        <th class="text-center">{{ trans('app.status') }}</th>
        <th>{{ trans('receipt.location') }}</th>
        <th>{{ trans('receipt.network') }}</th>
        <th>{{ trans('receipt.officer') }}</th>
    </thead>
    <tbody>
        <?php
            $sumTotal = 0;
            $sumItemCount = 0;
            $sumItemWeight = 0;
        ?>
        @forelse($receipts as $key => $receipt)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td class="text-center">{{ $receipt->pickup_time->format('Y-m-d') }}</td>
            <td>{{ $receipt->numberLink() }}</td>
            {{-- <td class="text-right">{{ formatRp($receipt->amount) }}</td> --}}
            <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
            <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
            <td class="text-center">{{ $receipt->items_count }}</td>
            <td class="text-right">{{ $receipt->weight }}</td>
            <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
            <td>{{ $receipt->lastLocation() }}</td>
            <td>{{ $receipt->present()->creatorNetwork }}</td>
            <td>{{ $receipt->creator->name }}</td>
        </tr>
        <?php
            $sumItemCount += $receipt->items_count;
            $sumItemWeight += $receipt->weight;
            // $sumTotal += $receipt->amount;
            $sumTotal += $receipt->bill_amount;
        ?>
        @empty
        <tr>
            <td colspan="10">{{ trans('receipt.no_receipts') }}</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th class="text-right" colspan="2">{{ trans('app.count') }}</th>
            <th>{{ $receipts->count() }} {{ trans('receipt.receipt') }}</th>
            <th class="text-right">{{ formatRp($sumTotal) }}</th>
            <td>&nbsp;</td>
            <th class="text-center">{{ $sumItemCount }}</th>
            <th class="text-right">{{ $sumItemWeight }}</th>
            <td colspan="3">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</div>
@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
    {!! Html::style(url('css/plugins/select2.min.css')) !!}
    <style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        text-align: left;
    }
    </style>
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
    {!! Html::script(url('js/plugins/select2.min.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#dest_city_id,#customer_id').select2();
    $('#start_date,#end_date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true
    });
})();
</script>
@endsection
