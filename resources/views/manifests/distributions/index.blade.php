@extends('layouts.app')

@section('title', trans('manifest.distributions.list'))

@section('content')
<div class="pull-right">
    {!! html_link_to_route('manifests.distributions.create', trans('manifest.distributions.create'), [], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">{{ trans('manifest.distributions.list') }}</h2>

<div class="panel panel-default table-responsive">
<table class="table table-condensed table-hover">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th>{{ trans('app.date') }}</th>
        <th>{{ trans('manifest.number') }}</th>
        <th>{{ trans('manifest.creator') }}</th>
        <th>{{ trans('manifest.handler') }}</th>
        <th>{{ trans('manifest.distributions.dest_city') }}</th>
        <th class="text-right">{{ trans('receipt.receipt') }}</th>
        <th class="text-right">{{ trans('receipt.pcs_count') }}</th>
        <th class="text-right">{{ trans('receipt.items_count') }}</th>
        <th class="text-right">{{ trans('manifest.weight') }}</th>
        <th>{{ trans('app.status') }}</th>
        <th>{{ trans('app.action') }}</th>
    </thead>
    <tbody>
        @forelse($manifests as $key => $manifest)
        <tr>
            <td>{{ $manifests->firstItem() + $key }}</td>
            <td>{{ $manifest->created_at->format('Y-m-d') }}</td>
            <td>
                {{ link_to_route('manifests.distributions.show', $manifest->number,[$manifest->number], [
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) }}
            </td>
            <td>{{ $manifest->present()->creatorName() }}</td>
            <td>{{ $manifest->present()->handlerName() }}</td>
            <td>{!! $manifest->dest_city_id ? $manifest->destinationCity->name : '-' !!}</td>
            <td class="text-right">{{ $manifest->receipts_count }}</td>
            <td class="text-right">{{ $manifest->receipts->sum('pcs_count') }}</td>
            <td class="text-right">{{ $manifest->receipts->sum('items_count') }}</td>
            <td class="text-right">{{ $manifest->present()->weight }}</td>
            <td>
                <span class="label label-{{ $manifest->present()->status['class'] }}">
                    {{ $manifest->present()->status['name'] }}
                </span>
            </td>
            <td>
                {!! html_link_to_route('manifests.distributions.show', '', [$manifest->number],[
                    'icon' => 'search',
                    'title' => 'Lihat detail manifest ' . $manifest->number,
                    'class' => 'btn btn-info btn-xs',
                ]) !!}
                @if($manifest->receipts_count)
                {!! html_link_to_route('manifests.distributions.pdf', '', [$manifest->number], [
                    'icon' => 'print',
                    'title' => trans('manifest.pdf'),
                    'target' => '_blank',
                    'class' => 'btn btn-success btn-xs',
                ]) !!}
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9">{{ trans('manifest.empty') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
{!! str_replace('/?', '?', $manifests->appends(Request::except('page'))->render()) !!}
@endsection
