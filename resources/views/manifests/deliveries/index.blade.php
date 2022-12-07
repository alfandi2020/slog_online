@extends('layouts.app')

@section('title', __('manifest.deliveries.list'))

@section('content')
<div class="pull-right">
    {!! html_link_to_route('manifests.deliveries.create', __('manifest.deliveries.create'), [], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">{{ __('manifest.deliveries.list') }}</h2>

<!-- Nav tabs -->
<ul class="nav nav-tabs bar_tabs">
    <li class="{{ (Request::has('type') == false || Request::get('type') == 'out') ? 'active' : '' }}">
        {!! html_link_to_route('manifests.deliveries.index', __('manifest.deliveries.out'), ['type' => 'out'], ['icon' => 'sign-out']) !!}
    </li>
    <li class="{{ Request::get('out') == 'out' ? 'active' : '' }}">
        {!! html_link_to_route('manifests.deliveries.index', __('manifest.deliveries.in', ['mynetwork' => $userNetwork->name]), ['type' => 'in'], ['icon' => 'sign-in']) !!}
    </li>
</ul>

<br>
<div class="panel panel-default table-responsive">
<table class="table table-condensed">
    <thead>
        <th>{{ __('app.table_no') }}</th>
        <th>{{ __('app.date') }}</th>
        <th>{{ __('manifest.number') }}</th>
        <th>{{ __('manifest.orig_network') }}</th>
        <th>{{ __('manifest.dest_network') }}</th>
        <th>{{ __('manifest.deliveries.delivery_unit') }}</th>
        <th class="text-right">{{ __('receipt.receipt') }}</th>
        <th class="text-right">{{ __('manifest.weight') }}</th>
        <th>{{ __('app.status') }}</th>
        <th>{{ __('app.action') }}</th>
    </thead>
    <tbody>
        @forelse($manifests as $key => $manifest)
        <tr>
            <td>{{ $manifests->firstItem() + $key }}</td>
            <td>{{ $manifest->created_at->format('Y-m-d') }}</td>
            <td>
                {{ link_to_route('manifests.deliveries.show', $manifest->number,[$manifest->number], [
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) }}
            </td>
            <td>{{ $manifest->originName() }}</td>
            <td>{{ $manifest->destinationName() }}</td>
            <td>{{ $manifest->deliveryCourier->name }}</td>
            <td class="text-right">{{ $manifest->receipts_count ?: $manifest->receipts->count() }}</td>
            <td class="text-right">{{ $manifest->weight ?: $manifest->receipts->sum('weight') }}</td>
            <td><span class="label label-{{ $manifest->present()->status['class'] }}">{{ $manifest->present()->status['name'] }}</span></td>
            <td>
                {!! html_link_to_route('manifests.deliveries.show', '', [$manifest->number],[
                    'icon' => 'search',
                    'title' => 'Lihat detail manifest ' . $manifest->number,
                    'class' => 'btn btn-info btn-xs',
                ]) !!}
                @if($manifest->receipts_count)
                {!! html_link_to_route('manifests.deliveries.pdf', '', [$manifest->number], [
                    'icon' => 'print',
                    'title' => __('manifest.pdf'),
                    'target' => '_blank',
                    'class' => 'btn btn-success btn-xs',
                ]) !!}
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9">{{ __('manifest.empty') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
{!! str_replace('/?', '?', $manifests->appends(Request::except('page'))->render()) !!}
@endsection
