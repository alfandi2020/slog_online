@inject('paymentTypes','App\Entities\Receipts\PaymentType')
@extends('layouts.app')

@section('title', trans('report.omzet.daily') . ' : ' . dateId($receiptQuery['start_date']) . ' s/d ' .  dateId($receiptQuery['end_date']))

@section('content')
<?php
// TODO: add report lang
?>

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
{!! FormField::text('start_date', ['label' => trans('app.from'),'value' => $receiptQuery['start_date'], 'style' => 'width:100px']) !!}
{!! FormField::text('end_date', ['label' => trans('app.to'),'value' => $receiptQuery['end_date'], 'style' => 'width:100px']) !!}
{!! FormField::select('payment', $paymentTypes::dropdown(), [
    'label' => 'Pembayaran',
    'value' => request('payment', 1),
    'placeholder' => false,
]) !!}
{!! Form::submit('Lihat Laporan', ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.sales-counter.daily', 'Lihat Hari Ini', [], ['class'=>'btn btn-default']) !!}
@if (! $receipts->isEmpty())
{!! html_link_to_route('reports.sales-counter.daily', 'Download Excel', Request::only([
    'start_date', 'end_date',
    'payment',
]) + ['export' => 1], ['icon' => 'file-excel-o', 'class'=>'btn btn-default']) !!}
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
        <th>{{ trans('receipt.officer') }}</th>
        <th>{{ trans('app.action') }}</th>
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
            <td>{{ $receipt->number }}</td>
            <td class="text-right">{{ formatRp($receipt->amount) }}</td>
            <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
            <td class="text-center">{{ $receipt->items_count }}</td>
            <td class="text-right">{{ $receipt->weight }}</td>
            <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
            <td>{{ $receipt->creator->name }}</td>
            <td>
                {!! link_to_route('receipts.show',trans('app.show'),[$receipt->number],[
                    'class'=>'btn btn-success btn-xs',
                    'title'=>'Lihat Detail ' . $receipt->number,
                    'target'=>'_blank'
                ]) !!}
            </td>
        </tr>
        <?php
        $sumItemCount += $receipt->items_count;
        $sumItemWeight += $receipt->weight;
        $sumTotal += $receipt->amount;
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
            <th class="text-center">{{ $sumItemWeight }}</th>
            <td colspan="3">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</div>
@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#start_date,#end_date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true
    });
})();
</script>
@endsection