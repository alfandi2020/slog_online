@extends('layouts.receipt-detail')

@section('subtitle', trans('receipt.manifests'))

@section('receipt-content')
<div class="panel panel-default table-responsive">
    <div class="panel-body">
    <table class="table table-condensed">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th class="col-sm-2">{{ trans('manifest.number') }}</th>
            <th class="col-sm-2">{{ trans('manifest.orig_network') }}</th>
            <th class="col-sm-2">{{ trans('manifest.dest_network') }}</th>
            <th class="col-sm-2 text-right">{{ trans('manifest.duration') }}</th>
            <th class="col-sm-3 ">{{ trans('app.notes') }}</th>
            <th class="col-sm-1 ">{{ trans('app.status') }}</th>
        </thead>
        <tbody>
            @foreach($receipt->manifests as $key => $manifest)
            <tr class="">
                <td>{{ 1 + $key }}</td>
                <td><div>{!! $manifest->present()->numberLink !!}</div><span class="badge" style="background-color:{{ $manifest->typeColor() }}">{{ $manifest->type() }}</span></td>
                <td>
                    <div>{{ $manifest->originNetwork->name }}</div>
                    <div class="text-info small">{{ trans('manifest.created_by') }}: {{ $manifest->creator->name }}</div>
                    <div class="text-danger small">{{ $manifest->deliver_at ? $manifest->deliver_at->format('d M Y (H:i)') : null }}</div>
                </td>
                <td>
                    <div>{{ $manifest->destinationNetwork->name }}</div>
                    <div class="text-info small">{{ trans('manifest.handled_by') }}: {{ $manifest->handler ? $manifest->handler->name : '-' }}</div>
                    <div class="text-danger small">{{ $manifest->received_at ? $manifest->received_at->format('d M Y (H:i)') : null }}</div>
                </td>
                <td class="text-right">{{ $manifest->received_at ? $manifest->deliver_at->diffInDays($manifest->received_at) . ' Hari' : '' }}</td>
                <td>{{ $manifest->notes }}</td>
                <td>{!! $manifest->present()->statusLabel !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection