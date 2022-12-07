<?php
$months = getMonths();
// TODO: add report lang
?>
@extends('layouts.app')

@section('title')
Laporan Penjualan Komoditas Bulanan : {{ $months[$month] }} {{ $year }}
@if ($comodity)
| {{ $comodity->name }}
@endif
@endsection

@section('content')

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
{!! Form::select('month', $months, $month, ['class'=>'form-control']) !!}
{!! Form::select('year', getYears(), $year, ['class'=>'form-control']) !!}
{!! Form::select('comodity_id', $comodities, Request::get('comodity_id'), ['class'=>'form-control','placeholder' => '-- Semua Komoditas --']) !!}
{!! Form::submit('Lihat Laporan', ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.comodity-omzet.monthly','Bulan ini',[],['class'=>'btn btn-default']) !!}
{!! link_to_route('reports.comodity-omzet.yearly','Lihat Tahunan',['year' => $year],['class'=>'btn btn-default']) !!}
{!! Form::close() !!}

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Grafik Penjualan Komoditas {{ $months[$month] }} {{ $year }}
            @if ($comodity)
            | {{ $comodity->name }}
            @endif
        </h3>
    </div>
    <div class="panel-body">
        <strong>Rp.</strong>
        <div id="monthly-chart" style="height: 350px;"></div>
        <div class="text-center"><strong>Tanggal</strong></div>
    </div>
</div>
@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/morris.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/raphael.min.js')) !!}
    {!! Html::script(url('js/plugins/morris.min.js')) !!}
@endpush

@section('script')
<script>
(function() {
    new Morris.Line({
        element: 'monthly-chart',
        data: {!! json_encode($reports) !!},
        xkey: 'date',
        xLabelAngle: 45,
        ykeys: {!! json_encode(array_values($comodities)) !!},
        labels: {!! json_encode(array_values($comodities)) !!},
        xLabels: 'day',
        parseTime:false
    });
})();
</script>
@endsection
