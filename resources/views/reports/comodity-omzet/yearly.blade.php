@extends('layouts.app')

@section('title')
Laporan Tahunan : {{ $year }}
@if ($comodity)
| {{ $comodity->name }}
@endif
@endsection

@section('content')
<?php
// TODO: add report lang
?>

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
{!! Form::select('year', getYears(), $year, ['class'=>'form-control']) !!}
{!! Form::select('comodity_id', $comodities, Request::get('comodity_id'), ['class'=>'form-control','placeholder' => '-- Semua Komoditas --']) !!}
{!! Form::submit('Lihat Laporan', ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.comodity-omzet.yearly','Tahun ini',[],['class'=>'btn btn-default']) !!}
{!! Form::close() !!}

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Grafik Penjualan {{ $year }}
            @if ($comodity)
            | {{ $comodity->name }}
            @endif
        </h3>
    </div>
    <div class="panel-body">
        <strong>Rp.</strong>
        <div id="yearly-chart" style="height: 350px;"></div>
        <div class="text-center"><strong>Bulan</strong></div>
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
        element: 'yearly-chart',
        data: {!! json_encode($reports) !!},
        xkey: 'month',
        xLabels: 'month',
        ykeys: {!! json_encode(array_values($comodities)) !!},
        xLabelAngle: 30,
        labels: {!! json_encode(array_values($comodities)) !!},
        parseTime: false
    });
})();
</script>
@endsection
