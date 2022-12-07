@extends('layouts.app')

@section('title', trans('nav_menu.home'))

@section('content')
<div class="well well-sm">
    {{ Form::open(['route' => 'home', 'method' => 'get', 'class' => 'form-inline pull-right']) }}
    {{ Form::hidden('tab', request('tab')) }}
    {!! FormField::select('ym', $yearMonths, ['label' => 'Tahun-Bulan', 'placeholder' => 'Semua', 'value' => $queriedYearMonths]) !!}
    {{ Form::submit('Lihat Summary', ['class' => 'btn btn-info']) }}
    {{ link_to_route('home', 'Reset', [], ['class' => 'btn btn-default']) }}
    {{ Form::close() }}
    <h3 style="margin:4px 0">Dashboard</h3>
    <div class="clearfix"></div>
</div>
@include('pages.partials.dashboard-nav-tabs')

@includeWhen($receiptMonitorData, 'pages.partials.dashboard-all-receipt-monitor')
@includeWhen($receiptPaymentMonitorData, 'pages.partials.dashboard-receipt-monitor')
@includeWhen($retainedReceiptsData, 'pages.partials.dashboard-receipt-retained')
@includeWhen($invoiceableReceiptsData, 'pages.partials.dashboard-receipt-uninvoiced-pod')

<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Monitor Users</h3></div>
    <table class="table table-condensed table-bordered table-hover small-text">
        <thead>
            <tr>
                <th>{{ trans('app.table_no') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('user.network') }}</th>
                <th>{{ trans('user.role') }}</th>
                <th>Status</th>
                <th>Terakhir Terlihat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usersStatus as $key => $userStatus)
                <tr>
                    <td>{{ 1 + $key }}</td>
                    <td>{{ $userStatus->name }}</td>
                    <td>{{ $userStatus->network->name }}</td>
                    <td>{{ $userStatus->role() }}</td>
                    <td>
                        @if(Cache::has('user-is-online-' . $userStatus->id))
                            <span class="label label-success">Online</span>
                        @else
                            <span class="label label-default">Offline</span>
                        @endif
                    </td>
                    <td>
                        {{ Carbon\Carbon::parse($userStatus->last_seen)->diffForHumans() }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <legend>Entry Resi Baru</legend>
                {!! FormField::formButton(['route' => 'receipts.add-receipt'], trans('receipt.create'), [
                    'class' => 'btn btn-default'
                ], ['service_id' => 14, 'orig_city_id' => auth()->user()->network->origin_city_id]) !!}
                {!! FormField::formButton(['route' => 'receipts.add-project-receipt'], trans('receipt.41.create'), [
                    'class' => 'btn btn-default','title' => trans('receipt.41.create_link_title')
                ], ['service_id' => 41]) !!}
            </div>
        </div>
    </div>
    <div class="col-lg-4">@include('pages.partials.manifest-summary')</div>
    <div class="col-lg-4">@include('pages.partials.latest-receipts')</div>
</div>
@endsection
