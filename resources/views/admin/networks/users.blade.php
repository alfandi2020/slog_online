@extends('layouts.app')

@section('title', trans('user.list'))

@section('content')

<div class="pull-right">
    {!! link_to_route('admin.users.create', trans('user.create'), ['network_id' => $network->id], ['class' => 'btn btn-success']) !!}
</div>
<h2 class="page-header">{{ $network->name }} <small>{{ trans('user.list') }} : {{ $network->users->count() }}</small></h2>

@include('admin.networks.partials.nav-tabs')

<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>{{ trans('app.table_no') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('user.username') }}</th>
                <th>{{ trans('contact.email') }}</th>
                <th>{{ trans('user.role') }}</th>
                <th>{{ trans('user.network') }}</th>
                <th class="text-center">{{ trans('user.is_active') }}</th>
                <th>{{ trans('app.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($network->users as $key => $user)
            <tr>
                <td>{{ 1 + $key }}</td>
                <td>{{ $user->nameLink() }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role() }}</td>
                <td>{{ $user->network->name }}</td>
                <td class="text-center">{{ $user->status }}</td>
                <td>{!! $user->present()->showLink !!} | {!! $user->present()->editLink !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
