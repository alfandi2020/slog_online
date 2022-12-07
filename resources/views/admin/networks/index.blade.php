@extends('layouts.app')

@section('title', trans('network.list'))

@section('content')
<div class="pull-right">
    {!! Form::open(['method'=>'get','class'=>'form-inline']) !!}
    {!! Form::text('q', Request::get('q'), ['class'=>'form-control','placeholder'=>trans('network.search'),'style' => 'width:250px']) !!}
    {!! Form::submit(trans('network.search'), ['class' => 'btn btn-info']) !!}
    {!! link_to_route('admin.networks.index','Reset',[],['class' => 'btn btn-default']) !!}
    {!! link_to_route('admin.networks.create', trans('network.create'), [], ['class'=>'btn btn-success']) !!}
    {!! Form::close() !!}
</div>
<h2 class="page-header">
    {{ trans('network.list') }} <small>{{ trans('app.total') }} : {{ $networks->total() }}</small>
</h2>
<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('app.code') }}</th>
            <th>{{ trans('network.name') }}</th>
            <th>{{ trans('address.address') }}</th>
            <th>{{ trans('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($networks as $key => $network)
            <tr>
                <td>{{ $networks->firstItem() + $key }}</td>
                <td>{{ $network->code }}</td>
                <td>{{ $network->name }}</td>
                <td>{{ $network->address }}</td>
                <td>
                    {!! html_link_to_route('admin.networks.show', '',[$network->id], ['icon' => 'search', 'title'=>trans('network.show')]) !!} |
                    {!! html_link_to_route('admin.networks.edit', '',[$network->id], ['icon' => 'edit', 'title'=>trans('network.edit')]) !!}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">{{ trans('network.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="panel-body">{!! str_replace('/?', '?', $networks->appends(Request::except('page'))->render()) !!}</div>
</div>
@endsection
