@extends('layouts.app')

@section('title', trans('user.list'))

@section('content')

<div class="pull-right">
    {!! Form::open(['method'=>'get','class' => 'form-inline']) !!}
    {!! FormField::select('network_id', $networks, ['value' => request('network_id')]) !!}
    {!! Form::submit('Filter', ['class' => 'btn btn-info']) !!}
    {!! link_to_route('admin.users.index', 'Reset', [], ['class' => 'btn btn-default']) !!}
    {{ link_to_route('admin.users.create', trans('user.create'), [], ['class' => 'btn btn-success']) }}
    {!! Form::close() !!}
</div>

<h2 class="page-header">{{ trans('user.list') }} <small>{{ trans('app.total') }} : {{ $users->total() }}</small></h2>

<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>{{ trans('app.table_no') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('user.username') }}</th>
                <th class="text-center">{{ trans('contact.phone') }}</th>
                <th>{{ trans('contact.email') }}</th>
                <th class="text-center">{{ trans('user.role') }}</th>
                <th>{{ trans('user.network') }}</th>
                <th class="text-center">{{ trans('user.is_active') }}</th>
                <th>{{ trans('app.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $key => $user)
            <tr>
                <td>{{ 1 + $key }}</td>
                <td>{{ $user->nameLink() }}</td>
                <td>{{ $user->username }}</td>
                <td class="text-center">{{ $user->phone }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">{{ $user->role() }}</td>
                <td>{{ link_to_route('admin.networks.users', $user->network->name, $user->network_id) }}</td>
                <td class="text-center">{{ $user->status }}</td>
                <td>{!! $user->present()->showLink !!} | {!! $user->present()->editLink !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{!! $users->appends(Request::except('page'))->render() !!}
@endsection
