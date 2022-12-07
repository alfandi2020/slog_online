@inject('roles', 'App\Entities\Users\Role')
@extends('layouts.app')

@section('title', trans('user.create'))

@section('content')
<h3 class="page-header text-center">{{ trans('user.create') }}</h3>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        {!! Form::open(['route' => 'admin.users.store']) !!}
        <div class="row">
            <div class="col-md-6">{!! FormField::text('username', ['label' => trans('user.username')]) !!}</div>
            <div class="col-md-6">{!! FormField::text('name', ['label' => trans('user.name')]) !!}</div>
        </div>
        <div class="row">
            <div class="col-md-7">{!! FormField::email('email', ['label' => trans('contact.email')]) !!}</div>
            <div class="col-md-5">{!! FormField::text('phone', ['label' => trans('contact.phone')]) !!}</div>
        </div>
        <div class="row">
            <div class="col-md-6">{!! FormField::select('network_id', $networks, ['label' => trans('user.network'), 'value' => Request::get('network_id')]) !!}</div>
            <div class="col-md-6">{!! FormField::text('password',['info' => ['text' => 'Password Default: <code>' . Option::get('default_password', 'secret') . '</code>']]) !!}</div>
        </div>
        <div class="row">
            <div class="col-md-6">{!! FormField::radios('role_id', $roles->toArray(), ['value' => 1]) !!}</div>
            <div class="col-md-6">{!! FormField::radios('gender_id', [1 => 'Laki-laki', 'Perempuan'], ['label' => trans('user.gender'), 'list_style' =>'unstyled', 'value' => 1]) !!}</div>
        </div>
        <br>

        {!! Form::submit(trans('user.create'), [
            'class' => 'btn btn-success',
            'name' => Request::has('network_id') ? 'create_and_go_to_network' : false
        ]) !!}
        @if (Request::has('network_id'))
        {{ link_to_route('admin.networks.users', trans('app.cancel'), [Request::get('network_id')], ['class' => 'btn btn-default']) }}
        @else
        {{ link_to_route('admin.users.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
        @endif
        {!! Form::close() !!}
    </div>
</div>
@endsection