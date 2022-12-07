@extends('layouts.app')

@section('title', __('report.time_series.omzet').': '.__('report.time_series.omzet'))

@section('content')
<div class="well well-sm">
    <h3 class="pull-left" style="margin-top: 4px; margin-bottom: 0px;">{{ __('report.time_series.omzet') }}</h3>
    {!! Form::open(['method' => 'get', 'class' => 'form-inline text-right']) !!}
    {!! FormField::select('year', getYears(), ['value' => $year, 'placeholder' => false, 'label' => 'Pilih Tahun']) !!}
    {!! Form::submit(__('report.view_report'), ['class' => 'btn btn-info']) !!}
    {!! link_to_route('reports.time_series.omzet', __('report.this_year'), [], ['class' => 'btn btn-default']) !!}
    {!! Form::close() !!}
</div>

@php
    $monthlyTotal = [];
@endphp

@foreach (App\Entities\Receipts\PaymentType::toArray() as $paymentTypeId => $paymentType)
@php
    $monthlyTotal[$paymentTypeId] = array_map(function () {
        return 0;
    }, getMonths());
@endphp
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="badge pull-right">{{ $year }}</span>
            {{ $paymentType }}
        </h3>
    </div>
    <table class="table table-condensed table-hover" style="font-size:11px">
        <thead>
            <tr>
                <th class="text-right">{{ __('app.table_no') }}</th>
                <th>{{ __('customer.customer') }}</th>
                @foreach (getMonths() as $month)
                    <th class="text-right">{{ $month }}</th>
                @endforeach
                <th class="text-right">{{ __('app.total') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-right">{{ 1 }}</td>
                <td>UMUM</td>
                @php
                    $monthNonCustomerRecapTotal = $omzetTimeSeriesRecap->filter(function ($item) use ($paymentTypeId) {
                        return $item->customer_id == null && $item->payment_type_id == $paymentTypeId;
                    })->sum('bill_total');
                @endphp
                @foreach (getMonths() as $monthNum => $month)
                    @php
                        $monthlyOmzetTimeSeriesRecap = $omzetTimeSeriesRecap->filter(function ($item) use ($monthNum, $paymentTypeId) {
                            return $item->customer_id == null && $item->month == (int) $monthNum && $item->payment_type_id == $paymentTypeId;
                        })->first();
                        $billRecap = $monthlyOmzetTimeSeriesRecap ? $monthlyOmzetTimeSeriesRecap->bill_total : 0;
                    @endphp
                    <td class="text-right">{{ formatNo($billRecap) }}</td>
                    @php
                        $monthlyTotal[$paymentTypeId][$monthNum] += $billRecap;
                    @endphp
                @endforeach
                <td class="text-right">{{ formatNo($monthNonCustomerRecapTotal) }}</td>
            </tr>
            @php
                $number = 2;
            @endphp
            @foreach ($customers as $key => $customer)
            @php
                $monthCustomerRecapTotal = $omzetTimeSeriesRecap->filter(function ($item) use ($customer, $paymentTypeId) {
                    return $item->customer_id == $customer->id && $item->payment_type_id == $paymentTypeId;
                })->sum('bill_total');
            @endphp
            @continue(!$monthCustomerRecapTotal)
            <tr>
                <td class="text-right">{{ ++$number }}</td>
                <td>{{ $customer->present()->nameLink }}</td>
                @foreach (getMonths() as $monthNum => $month)
                    @php
                        $monthlyOmzetTimeSeriesRecap = $omzetTimeSeriesRecap->filter(function ($item) use ($customer, $monthNum, $paymentTypeId) {
                            return $item->customer_id == $customer->id && $item->month == (int) $monthNum && $item->payment_type_id == $paymentTypeId;
                        })->first();
                        $billRecap = $monthlyOmzetTimeSeriesRecap ? $monthlyOmzetTimeSeriesRecap->bill_total : 0;
                    @endphp
                    <td class="text-right">{{ formatNo($billRecap) }}</td>
                    @php
                        $monthlyTotal[$paymentTypeId][$monthNum] += $billRecap;
                    @endphp
                @endforeach
                <td class="text-right">{{ formatNo($monthCustomerRecapTotal) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-center">{{ __('app.total') }}</th>
                @foreach (getMonths() as $monthNum => $month)
                    <th class="text-right">{{ formatNo($monthlyTotal[$paymentTypeId][$monthNum]) }}</th>
                @endforeach
                <th class="text-right">{{ formatNo(array_sum($monthlyTotal[$paymentTypeId])) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endforeach

@php
    $monthlyRecapTotal = array_map(function () {
        return 0;
    }, getMonths());

    foreach ($monthlyTotal as $paymentTypeId => $paymentTypeRecap) {
        foreach ($paymentTypeRecap as $monthNumber => $recap) {
            $monthlyRecapTotal[$monthNumber] += $recap;
        }
    }
@endphp

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="badge pull-right">{{ $year }}</span>
            {{ __('app.total') }}
        </h3>
    </div>
    <table class="table table-condensed table-hover" style="font-size:11px">
        <thead>
            <tr>
                <th colspan="2">&nbsp;</th>
                @foreach (getMonths() as $month)
                    <th class="text-right">{{ $month }}</th>
                @endforeach
                <th class="text-right">{{ __('app.total') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="2" class="text-center">Grand Total</th>
                @foreach (getMonths() as $monthNum => $month)
                    <th class="text-right">{{ formatNo($monthlyRecapTotal[$monthNum]) }}</th>
                @endforeach
                <th class="text-right">{{ formatNo(array_sum($monthlyRecapTotal)) }}</th>
            </tr>
        </tbody>
    </table>
</div>
@endsection
