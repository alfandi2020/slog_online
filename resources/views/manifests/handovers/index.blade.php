@extends('layouts.app')

@section('title', trans('manifest.handovers.list'))

@section('content')
<div class="pull-right">
    {!! html_link_to_route('manifests.handovers.create', trans('manifest.handovers.create'), [], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">{{ trans('manifest.handovers.list') }}</h2>

<div class="panel panel-default table-responsive">
<table class="table table-condensed">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th>{{ trans('app.date') }}</th>
        <th>{{ trans('manifest.number') }}</th>
        <th>{{ trans('manifest.creator') }}</th>
        <th>{{ trans('manifest.handler') }}</th>
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
                {{ link_to_route('manifests.handovers.show', $manifest->number,[$manifest->number], [
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) }}
            </td>
            <td>{{ $manifest->present()->creatorName() }}</td>
            <td>{{ $manifest->present()->handlerName() }}</td>
            <td class="text-right">{{ $manifest->receipts_count }}</td>
            <td class="text-right">{{ $manifest->present()->weight }}</td>
            <td>
                <span class="label label-{{ $manifest->present()->status['class'] }}">
                    {{ $manifest->present()->status['name'] }}
                </span>
            </td>
            <td>
                {!! html_link_to_route('manifests.handovers.show', '', [$manifest->number],[
                    'icon' => 'search',
                    'title' => 'Lihat detail manifest ' . $manifest->number,
                    'class' => 'btn btn-info btn-xs',
                ]) !!}
                @if($manifest->receipts_count)
                {!! html_link_to_route('manifests.handovers.pdf', '', [$manifest->number], [
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
