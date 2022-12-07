@extends('layouts.receipt-detail')

@section('subtitle', trans('receipt.progress'))

@section('receipt-content')
<div class="panel panel-default table-responsive">
    <div class="panel-body">
    <table class="table table-hover table-condensed">
        <thead>
            <th>{{ trans('app.time') }}</th>
            <th>{{ trans('receipt.location') }}</th>
            <th>{{ trans('receipt.status') }}</th>
            <th>{{ trans('receipt.handler') }}</th>
            <th>{{ trans('app.notes') }}</th>
        </thead>
        <tbody>
            @foreach($receipt->progressList() as $key => $progress)
            <tr class="">
                <td>{{ $progress['time'] }}</td>
                <td>
                    {{ $progress['stop'] }}
                </td>
                <td>
                    {!! $progress['status'] !!}
                    @if(in_array($progress['status_code'], ['dl', 'bd']))
                    [{{ $receipt->consignee['recipient'] }}]
                    @endif
                </td>
                <td>{{ $progress['handler'] }}</td>
                <td>{{ $progress['notes'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-left">
        {{-- <span>Lama Pengiriman: {{ (Carbon\Carbon::parse($receipt->created_at))->diffInDays(! is_null(Carbon\Carbon::parse($receipt->proof)) ? ) }} Hari</span> --}}
    </div>
    </div>
</div>
@endsection
