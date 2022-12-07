@extends('layouts.app')

@section('title', trans('manifest.accountings.list'))

@section('content')
<div class="pull-right">
    {!! html_link_to_route('manifests.accountings.create', trans('manifest.accountings.create'), [], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">{{ trans('manifest.accountings.list') }}</h2>

<div class="panel panel-default table-responsive">
<table class="table table-condensed">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th>{{ trans('app.date') }}</th>
        <th>{{ trans('manifest.number') }}</th>
        <th>{{ trans('customer.customer') }}</th>
        <th class="text-center">{{ trans('manifest.creator') }}</th>
        <th class="text-center">{{ trans('manifest.handler') }}</th>
        <th class="text-center">{{ trans('manifest.receipts_count') }}</th>
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
                {{ link_to_route('manifests.accountings.show', $manifest->number,[$manifest->number], [
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) }}
            </td>
            <td>{{ $manifest->present()->customerName() }}</td>
            <td class="text-center">{{ $manifest->present()->creatorName() }}</td>
            <td class="text-center">{{ $manifest->present()->handlerName() }}</td>
            <td class="text-center">{{ $manifest->receipts_count }}</td>
            <td class="text-right">{{ $manifest->present()->weight }}</td>
            <td>{!! $manifest->present()->statusLabel !!}</td>
            <td>
                {!! html_link_to_route('manifests.accountings.show', '', [$manifest->number],[
                    'icon' => 'search',
                    'title' => 'Lihat detail manifest ' . $manifest->number,
                    'class' => 'btn btn-info btn-xs',
                ]) !!}
                @if($manifest->receipts_count)
                {!! html_link_to_route('manifests.accountings.pdf', '', [$manifest->number], [
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
