@extends('layouts.app')

@section('title', trans('user.edit'))

@section('content')

@if (Request::get('action') == 'delete' && $user)
@can('delete', $user)
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('user.delete') }}</h3></div>
            <div class="panel-body">
                <label class="control-label">{{ trans('user.dob') }}</label>
                <p>{{ $user->dob }}</p>
                <label class="control-label">{{ trans('user.name') }}</label>
                <p>{{ $user->name }}</p>
                <label class="control-label">{{ trans('user.email') }}</label>
                <p>{{ $user->email }}</p>
                <label class="control-label">{{ trans('user.role') }}</label>
                <p>{{ $user->role_id }}</p>
            </div>
            <hr style="margin:0">
            <div class="panel-body">
                {{ trans('app.delete_confirm') }}
            </div>
            <div class="panel-footer">
                {!! FormField::delete(['route'=>['admin.users.destroy',$user->id]], trans('app.delete_confirm_button'), ['class'=>'btn btn-danger'], ['user_id'=>$user->id]) !!}
                {{ link_to_route('admin.users.edit', trans('app.cancel'), [$user->id], ['class' => 'btn btn-default']) }}
            </div>
        </div>
    </div>
</div>
@endcan
@endif

<h3 class="page-header text-center">{{ trans('user.edit') }}</h3>

@inject('roles', 'App\Entities\Users\Role')

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        {!! Form::model($user, ['route' => ['admin.users.update', $user->id],'method' => 'patch']) !!}
        <div class="row">
            <div class="col-md-6">{!! FormField::text('username', ['label' => trans('user.username')]) !!}</div>
            <div class="col-md-6">{!! FormField::text('name', ['label' => trans('user.name')]) !!}</div>
        </div>
        <div class="row">
            <div class="col-md-7">{!! FormField::email('email', ['label' => trans('contact.email')]) !!}</div>
            <div class="col-md-5">{!! FormField::text('phone', ['label' => trans('contact.phone')]) !!}</div>
        </div>
        <div class="row">
            <div class="col-md-6">{!! FormField::select('network_id', $networks, ['label' => trans('user.network')]) !!}</div>
            <div class="col-md-6">{!! FormField::password('password',['info' => ['text' => trans('user.password_form_note'),'class' => 'text-info small']]) !!}</div>
        </div>
        <div class="row">
            <div class="col-md-6">{!! FormField::radios('role_id', $roles->toArray()) !!}</div>
            <div class="col-md-3">{!! FormField::radios('gender_id', [1 => 'Laki-laki', 'Perempuan'], ['label' => trans('user.gender'), 'list_style' =>'unstyled']) !!}</div>
            <div class="col-md-3">{!! FormField::radios('is_active', ['Non Aktif','Aktif'], ['list_style' =>'unstyled']) !!}</div>
        </div>
        <br>
        {!! Form::submit(trans('user.update'), ['class' => 'btn btn-success']) !!}
        {{ link_to_route('admin.users.show', trans('app.cancel'), [$user->id], ['class' => 'btn btn-default']) }}
        @can('delete', $user)
            {!! $user->present()->deleteLink !!}
        @endcan
        {!! Form::close() !!}
    </div>
</div>
<br>
@endsection
