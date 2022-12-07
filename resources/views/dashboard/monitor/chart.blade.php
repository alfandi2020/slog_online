@extends('layouts.no_sidebar')

@section('title', __('nav_menu.receipt_monitor_chart'))

@section('content')
<div class="text-right">
    {{ Form::open(['method' => 'get', 'class' => 'form-inline pull-right']) }}
    {{ Form::label('month', __('app.year_month'), ['class' => 'control-label']) }}
    {{ Form::select('month', getMonths(), request('month', date('m')), ['class' => 'form-control input-sm']) }}
    {{ Form::select('year', getYears(), request('year', date('Y')), ['class' => 'form-control input-sm']) }}
    {{ Form::select('network_id', ['' => '-- Semua Cabang --'] + $networkList, request('network_id'), ['class' => 'form-control input-sm']) }}
    {{ Form::select('payment_type_id', ['' => '-- Semua Pembayaran --'] + $paymentTypeList, request('payment_type_id'), ['class' => 'form-control input-sm']) }}
    {{ Form::submit(__('app.submit'), ['class' => 'btn btn-info btn-sm']) }}
    {{ link_to_route('dashboard.monitor.chart', __('app.reset'), [], ['class' => 'btn btn-default btn-sm']) }}
    {{ Form::close() }}
</div>
<h3 class="page-header">
    {{ __('nav_menu.receipt_monitor_chart') }} {{ monthId($month) }} {{ $year }}
</h3>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default table-responsive">
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('receipt.status') }} <div class="pull-right">{{ __('app.date') }}</div></th>
                        @foreach($monthDateArray as $date)
                        <th class="text-center">{{ $date }}</th>
                        @endforeach
                        <th class="text-center">{{ __('app.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $columnTotal = array_map(function ($date) {
                            return 0;
                        }, array_flip($monthDateArray));
                    @endphp
                    @foreach ($receiptStatusList as $statusCode => $status)
                        @php
                            $rowTotal = 0;
                        @endphp
                        <tr>
                            <td>{{ $status }}</td>
                            @foreach($monthDateArray as $date)
                                <td class="text-center">
                                    @php
                                        $dateSummary = $receiptSummary->filter(function ($summary) use ($statusCode, $yearMonth, $date) {
                                            return $summary->the_status_code == $statusCode && $summary->date == $yearMonth.'-'.$date;
                                        })->first();
                                        $count = optional($dateSummary)->receipt_total;
                                        $rowTotal += $count;
                                        $columnTotal[$date] += $count;
                                        $linkParams = [
                                            'start_date' => $yearMonth.'-'.$date,
                                            'end_date' => $yearMonth.'-'.$date,
                                            'status_code' => $statusCode
                                        ];
                                    @endphp
                                    {{ $count ? link_to_route('reports.receipt.export', $count, $linkParams) : '' }}
                                </td>
                            @endforeach
                            <td class="text-center">{{ $rowTotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right">{{ __('app.total') }}</th>
                        @foreach($monthDateArray as $date)
                            <td class="text-center">{{ $columnTotal[$date] }}</td>
                        @endforeach
                        <td class="text-center">{{ array_sum($columnTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
