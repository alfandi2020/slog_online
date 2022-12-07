@extends('layouts.app')

@section('title', trans('network.show'))

@section('content')

<div class="pull-right">
    {!! link_to_route('admin.networks.edit', trans('network.edit'), [$network->id], ['class' => 'btn btn-warning']) !!}
    {!! link_to_route('admin.networks.index', trans('network.back_to_index'), [], ['class' => 'btn btn-default']) !!}
</div>
<h2 class="page-header">{{ $network->name }} <small>{{ trans('network.show') }}</small></h2>

@include('admin.networks.partials.nav-tabs')

<div class="row">
    <div class="col-md-5">
        <div class="panel panel-default">
            <table class="table table-condensed">
                <tbody>
                    <tr><th class="col-xs-4">{{ trans('app.code') }}</th><td class="col-xs-8">{{ $network->code }}</td></tr>
                    <tr><th>{{ trans('network.name') }}</th><td>{{ $network->name }}</td></tr>
                    <tr><th>{{ trans('network.type') }}</th><td>{{ $network->type() }}</td></tr>
                    <tr><th>{{ trans('address.address') }}</th><td>{{ $network->address }}</td></tr>
                    <tr><th>{{ trans('address.coordinate') }}</th><td>{{ $network->coordinate }}</td></tr>
                    <tr><th>{{ trans('address.postal_code') }}</th><td>{{ $network->postal_code }}</td></tr>
                    <tr><th>{{ trans('contact.phone') }}</th><td>{{ $network->phone }}</td></tr>
                    <tr><th>{{ trans('contact.email') }}</th><td>{{ $network->email }}</td></tr>
                    <tr><th>{{ trans('network.origin') }}</th><td>{{ $network->fullOriginName() }}</td></tr>
                    <tr><th>{{ trans('network.created_at') }}</th><td>{{ $network->created_at }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Grafik Penjualan {{ date('Y') }}
                </h3>
            </div>
                <strong style="margin-left:20px">Rp.</strong>
                <div id="yearly-chart" style="height: 250px;"></div>
                <div class="text-center"><strong>Bulan</strong></div>
        </div>
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
        data: {!! json_encode($chartData) !!},
        xkey: 'month',
        xLabels: 'month',
        ykeys: ['value'],
        // xLabelAngle: 30,
        labels: ['Penjualan'],
        parseTime: false
    });
})();
</script>
@endsection
