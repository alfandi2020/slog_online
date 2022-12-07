<?php
$months = getMonths();
// TODO: add report lang
?>
@inject('paymentTypes','App\Entities\Receipts\PaymentType')
@extends('layouts.app')

@section('title')
Laporan Bulanan : {{ $months[$month] }} {{ $year }}
@if ($network)
| {{ $network->name }}
@endif
@endsection

@section('content')

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
{!! Form::select('month', $months, $month, ['class'=>'form-control']) !!}
{!! Form::select('year', getYears(), $year, ['class'=>'form-control']) !!}
{!! Form::select('payment', $paymentTypes::dropdown(), Request::get('payment'), ['class'=>'form-control']) !!}
{!! Form::select('network_id', ['' => '-- Semua Kantor Cabang --'] + $networks, Request::get('network_id'), ['class'=>'form-control']) !!}
{!! Form::submit('Lihat Laporan', ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.network-omzet.monthly', 'Bulan ini', Request::only('payment', 'network_id'), ['class'=>'btn btn-default']) !!}
{!! link_to_route('reports.network-omzet.yearly', 'Lihat Tahunan', ['year' => $year] + Request::only('payment', 'network_id'), ['class'=>'btn btn-default']) !!}
{!! Form::close() !!}

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Grafik Penjualan {{ $months[$month] }} {{ $year }}
            @if ($network)
            | {{ $network->name }}
            @endif
        </h3>
    </div>
    <div class="panel-body">
        <strong>Rp.</strong>
        <div id="monthly-chart" style="height: 250px;"></div>
        <div class="text-center"><strong>Tanggal</strong></div>
    </div>
</div>
<div class="panel panel-success">
    <div class="panel-heading"><h3 class="panel-title">Detail Laporan Bulanan : {{ $months[$month] }} {{ $year }}</h3></div>
    <div class="panel-body">
        <table class="table table-condensed">
            <thead>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Jumlah Receipt</th>
                <th class="text-right">Penjualan</th>
                <th class="text-center">Pilihan</th>
            </thead>
            <tbody>
                <?php
                $receiptsCount = 0;
                $sumTotal = 0;
                $chartData = [];
                ?>
                @foreach(monthDateArray($year, $month) as $dateNumber)
                <?php
                    $any = isset($reports[$dateNumber]);
                    $count = $any ? $reports[$dateNumber]->count : 0;
                    $total = $any ? $reports[$dateNumber]->total : 0;
                ?>
                <tr>
                    <td class="text-center">{{ dateId($date = $year . '-' . $month . '-' . $dateNumber) }}</td>
                    <td class="text-center">{{ $count }}</td>
                    <td class="text-right">{{ formatRp($total) }}</td>
                    <td class="text-center">
                        {!! link_to_route('reports.network-omzet.daily','Lihat Harian', [
                            'start_date' => $date,
                            'end_date' => $date,
                            'payment' => Request::get('payment'),
                            'network_id' => Request::get('network_id'),
                        ], [
                            'class' => 'btn btn-info btn-xs',
                            'title' => 'Lihat laporan harian ' . $date
                        ]) !!}</td>
                </tr>
                <?php
                $receiptsCount += $count;
                $sumTotal += $total;
                $chartData[] = ['date' => $dateNumber, 'value' => $total];
                ?>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center">{{ trans('app.total') }}</th>
                    <th class="text-center">{{ $receiptsCount }}</th>
                    <th class="text-right">{{ formatRp($sumTotal) }}</th>
                    <td></td>
                </tr>
            </tfoot>
        </table>
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
        data: {!! json_encode($chartData) !!},
        xkey: 'date',
        xLabelAngle: 45,
        ykeys: ['value'],
        labels: ['Penjualan'],
        parseTime:false
    });
})();
</script>
@endsection
