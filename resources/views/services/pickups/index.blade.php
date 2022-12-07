@extends('layouts.app')

@section('title', trans('pickup.list'))

@section('content')
<h2 class="page-header">
    <div class="pull-right">
        {{ link_to_route('pickups.create', trans('pickup.create'), [], ['class' => 'btn btn-success']) }}
    </div>
    {{ trans('pickup.list') }}
    <small>{{ trans('app.total') }} : {{ $pickups->total() }} {{ trans('pickup.pickup') }}</small>
</h2>

<div class="panel panel-default table-responsive">
    <div class="panel-heading">
        {{ Form::open(['method' => 'get','class' => 'form-inline']) }}
        {!! FormField::text('q', ['value' => request('q'), 'label' => trans('pickup.search'), 'class' => 'input-sm']) !!}
        {{ Form::submit(trans('pickup.search'), ['class' => 'btn btn-sm']) }}
        {{ link_to_route('pickups.index', trans('app.reset')) }}
        {{ Form::close() }}
    </div>
    <table class="table table-condensed">
        <thead>
            <tr>
                <th class="text-center">{{ trans('app.date') }}</th>
                <th class="text-center">{{ trans('app.table_no') }}</th>
                <th class="text-center">{{ trans('pickup.number') }}</th>
                <th>{{ trans('pickup.courier') }}</th>
                <th class="text-center">{{ trans('pickup.customers_count') }}</th>
                <th class="text-center">{{ trans('receipt.receipt') }}</th>
                <th class="text-center">{{ trans('app.pc') }}</th>
                <th class="text-center">{{ trans('app.item') }}</th>
                <th class="text-center">{{ trans('app.weight') }}</th>
                <th class="text-center">{{ trans('app.status') }}</th>
                <th class="text-center">{{ trans('app.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pickups as $key => $pickup)
            <tr>
                <td class="text-center">{{ $pickup->created_at->format('Y-m-d') }}</td>
                <td class="text-center">{{ $pickups->firstItem() + $key }}</td>
                <td class="text-center">{{ $pickup->numberLink() }}</td>
                <td>{{ $pickup->courier->name }}</td>
                <td class="text-center">{{ $pickup->customers_count }}</td>
                <td class="text-center">{{ $pickup->receipts_count }}</td>
                <td class="text-center">{{ $pickup->pcs_count }}</td>
                <td class="text-center">{{ $pickup->items_count }}</td>
                <td class="text-center">{{ $pickup->weight_total }}</td>
                <td class="text-center">{!! $pickup->status_label !!}</td>
                <td class="text-center">
                    {!! html_link_to_route('pickups.show', '', [$pickup],[
                        'icon' => 'search',
                        'title' => 'Lihat detail pickup ' . $pickup->number,
                        'class' => 'btn btn-info btn-xs',
                    ]) !!}
                    {!! html_link_to_route('pickups.pdf', '', [$pickup],[
                        'icon' => 'print',
                        'title' => 'Cetak perintah pickup ' . $pickup->number,
                        'class' => 'btn btn-default btn-xs',
                        'target' => '_blank',
                    ]) !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="panel-body">{{ $pickups->appends(Request::except('page'))->render() }}</div>
</div>
@endsection
