@extends('layouts.app')

@section('title', trans('manifest.search'))

@section('content')
{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm', 'id'=>'formChecker']) !!}
<div class="form-group">
    {!! Form::label('manifest_number', trans('manifest.search_page_label'), ['class'=>'control-label']) !!}
    {!! Form::text('manifest_number', Request::get('manifest_number'), ['class'=>'form-control','required','style' => 'width:200px']) !!}
    {!! Form::submit(trans('manifest.search'), ['class'=>'btn btn-info']) !!}
    {!! link_to_route('manifests.index', trans('app.reset'), [], ['class' => 'btn btn-default']) !!}
</div>
{!! Form::close() !!}

@if (empty($manifests))
<div class="alert alert-info">
    {{ trans('manifest.search_alert_info') }}
</div>
@else
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
            <td>{{ $manifest->present()->numberLink }}</td>
            <td>{{ $manifest->originName() }}</td>
            <td>{{ $manifest->destinationName() }}</td>
            <td class="text-right">{{ $manifest->receipts_count ?: $manifest->receipts->count() }}</td>
            <td class="text-right">{{ $manifest->weight ?: $manifest->receipts->sum('weight') }}</td>
            <td><span class="label label-{{ $manifest->present()->status['class'] }}">{{ $manifest->present()->status['name'] }}</span></td>
            <td>
                {!! html_link_to_route('manifests.'.$manifest->pluralTypeCode().'.show', '', [$manifest->number],[
                    'icon' => 'search',
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) !!}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9">{{ trans('manifest.not_found') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
{!! str_replace('/?', '?', $manifests->appends(Request::except('page'))->render()) !!}
@endif
<div id="cameraInit">Initlize Camera...Please Await</div>
<video id="video" width="300" height="300"></video>
@endsection
