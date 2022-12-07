@extends('layouts.app')

@section('title', trans('user.show') . ' | ' . $user->name)

@section('content')
<div class="pull-right">
    {{ link_to_route('admin.users.edit', trans('user.edit'), [$user->id], ['class' => 'btn btn-warning']) }}
    {{ link_to_route('admin.users.index', trans('user.back_to_index'), [], ['class' => 'btn btn-default']) }}
</div>
<h2 class="page-header">{{ trans('user.show') }} <small>{{ $user->name }}</small></h2>
<div class="row">
    <div class="col-sm-6 col-lg-offset-2">
        <div class="panel panel-default">
            <table class="table table-condensed">
                <tbody>
                    <tr><td>{{ trans('user.name') }}</td><td>{{ $user->name }}</td></tr>
                    <tr><td>{{ trans('user.username') }}</td><td>{{ $user->username }}</td></tr>
                    <tr><td>{{ trans('user.email') }}</td><td>{{ $user->email }}</td></tr>
                    <tr><td>{{ trans('user.phone') }}</td><td>{{ $user->phone }}</td></tr>
                    <tr><td>{{ trans('user.gender') }}</td><td>{{ $user->gender }}</td></tr>
                    <tr><td>{{ trans('user.network') }}</td><td>{{ $user->present()->networkName }}</td></tr>
                    <tr><td>{{ trans('user.is_active') }}</td><td>{{ $user->status }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
