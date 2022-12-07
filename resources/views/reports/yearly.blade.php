@inject('paymentTypes','App\Entities\Receipts\PaymentType')
@extends('layouts.app')

@section('title')
Laporan Tahunan : {{ $year }}
@if (request('customer_id') != null && request('customer_id') == 0)
| UMUM
@endif
@if ($customer)
| {{ $customer->present()->numberName }}
@endif
@endsection

@section('content')
<?php
    // TODO: add report lang
?>

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
{!! Form::select('year', getYears(), $year, ['class'=>'form-control']) !!}
{!! Form::select('payment', $paymentTypes::dropdown(), Request::get('payment'), ['class'=>'form-control']) !!}
{!! Form::select('customer_id', $customers, Request::get('customer_id'), ['class'=>'form-control','placeholder' => '-- Semua Customer --']) !!}
{!! Form::submit('Lihat Laporan', ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.omzet.yearly','Tahun ini',[],['class'=>'btn btn-default']) !!}
{!! Form::close() !!}

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Grafik Penjualan {{ $year }}
            @if (request('customer_id') != null && request('customer_id') == 0)
            | UMUM
            @endif
            @if ($customer)
            | {{ $customer->present()->numberName }}
            @endif
        </h3>
    </div>
    <div class="panel-body">
        <strong>Rp.</strong>
        <div id="yearly-chart" style="height: 250px;"></div>
        <div class="text-center"><strong>Bulan</strong></div>
    </div>
</div>
@if (!is_null($customer))
<div class="row">
    <div class="col-sm-4">
        @include('customers.partials.show')
    </div>
    <div class="col-sm-8">
@endif
<div class="panel panel-success">
    <div class="panel-heading"><h3 class="panel-title">Detail Laporan Tahunan : {{ $year }}</h3></div>
    <div class="panel-body">
        <table class="table table-condensed">
            <thead>
                <th class="text-center">Bulan</th>
                <th class="text-center">Jumlah Resi</th>
                <th class="text-right">Penjualan</th>
                <th class="text-center">Pilihan</th>
            </thead>
            <tbody>
                <?php
                    $receiptsCount = 0;
                    $sumTotal = 0;
                ?>
                @foreach(getMonths() as $monthNumber => $monthName)
                <?php
                    $any = isset($reports[$monthNumber]);
                    $count = $any ? $reports[$monthNumber]->count : 0;
                    $total = $any ? $reports[$monthNumber]->total : 0;
                ?>
                <tr>
                    <td class="text-center">{{ monthId($monthNumber) }}</td>
                    <td class="text-center">{{ $count }}</td>
                    <td class="text-right">{{ formatRp($total) }}</td>
                    <td class="text-center">
                        {!! link_to_route('reports.omzet.monthly','Lihat Bulanan',[
                            'month' => $monthNumber,
                            'year' => $year,
                            'payment' => Request::get('payment'),
                            'customer_id' => Request::get('customer_id'),
                        ], [
                            'class' => 'btn btn-info btn-xs',
                            'title' => 'Lihat laporan bulanan ' . monthId($monthNumber)
                        ]) !!}
                    </td>
                </tr>
                <?php
                    $receiptsCount += $count;
                    $sumTotal += $total;
                    // $chartData[] = ['month' => $year . '-' . $monthNumber, 'value' => $any ? $total : null];
                    $chartData[] = ['month' => monthId($monthNumber), 'value' => $total];
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

@if (!is_null($customer))
    </div>
</div>
@endif

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
        data: {!! json_encode($chartData) !!},
        xkey: 'month',
        xLabels: 'month',
        ykeys: ['value'],
        xLabelAngle: 30,
        labels: ['Penjualan'],
        parseTime: false
    });
})();
</script>
@endsection
