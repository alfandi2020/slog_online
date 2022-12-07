@extends('layouts.app')

@section('title', trans('user.search'))

@section('content')

<h1 class="page-header">
    {{ trans('user.search') }}
</h1>

{!! Form::open(['method' => 'get', 'class' => 'well well-sm form-inline']) !!}
{!! Form::text('q', Request::get('q'), ['class' => 'form-control', 'placeholder' => 'Ketik : Nama atau NIP','style' => 'width:340px']) !!}
{!! Form::submit(trans('user.search'), ['class' => 'btn btn-info', 'style' => 'padding: 4px 12px;']) !!}
{!! link_to_route('admin.users.search', trans('app.reset'), [], ['class' => 'btn btn-default', 'style' => 'padding: 4px 12px;']) !!}
{!! Form::close() !!}

@if (Request::has('q'))
<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>{{ trans('app.table_no') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('user.dob') }}</th>
                <th>{{ trans('user.email') }}</th>
                <th>{{ trans('user.role') }}</th>
                {{-- <th>{{ trans('user.network') }}</th> --}}
                <th>{{ trans('app.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $key => $user)
            <tr>
                <td>{{ 1 + $key }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->dob }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role() }}</td>
                {{-- <td>{{ $user->network->name }}</td> --}}
                <td>
                    {{-- {!! $user->present()->showLink !!} --}} show |
                    {{-- {!! $user->present()->editLink !!} --}} edit
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{!! $users->appends(Request::except('page'))->render() !!}
@endif
@endsection