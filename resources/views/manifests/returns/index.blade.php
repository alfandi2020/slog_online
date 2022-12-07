@extends('layouts.app')

@section('title', trans('manifest.returns.list'))

@section('content')
<div class="pull-right">
    {!! html_link_to_route('manifests.returns.create', trans('manifest.returns.create'), [], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">{{ trans('manifest.returns.list') }}</h2>

<!-- Nav tabs -->
<ul class="nav nav-tabs bar_tabs">
    <li class="{{ (Request::has('type') == false || Request::get('type') == 'out') ? 'active' : '' }}">
        {!! html_link_to_route('manifests.returns.index', trans('manifest.returns.out'), ['type' => 'out'], ['icon' => 'rotate-left']) !!}
    </li>
    <li class="{{ Request::get('out') == 'out' ? 'active' : '' }}">
        {!! html_link_to_route('manifests.returns.index', trans('manifest.returns.in', ['mynetwork' => $userNetwork->name]), ['type' => 'in'], ['icon' => 'rotate-right']) !!}
    </li>
</ul>

<br>
<div class="panel panel-default table-responsive">
<table class="table table-condensed">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th>{{ trans('app.date') }}</th>
        <th>{{ trans('manifest.number') }}</th>
        <th>{{ trans('manifest.orig_network') }}</th>
        <th>{{ trans('manifest.dest_network') }}</th>
        <th class="text-right">{{ trans('manifest.receipts_count') }}</th>
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
                {{ link_to_route('manifests.returns.show', $manifest->number,[$manifest->number], [
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) }}
            </td>
            <td>{{ $manifest->originName() }}</td>
            <td>{{ $manifest->destinationName() }}</td>
            <td class="text-right">{{ $manifest->receipts_count }}</td>
            <td class="text-right">{{ $manifest->weight }}</td>
            <td><span class="label label-{{ $manifest->present()->status['class'] }}">{{ $manifest->present()->status['name'] }}</span></td>
            <td>
                {!! html_link_to_route('manifests.returns.show', '', [$manifest->number],[
                    'icon' => 'search',
                    'title' => 'Lihat detail manifest ' . $manifest->number,
                    'class' => 'btn btn-info btn-xs',
                ]) !!}
                @if($manifest->receipts_count)
                {!! html_link_to_route('manifests.returns.pdf', '', [$manifest->number], [
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
