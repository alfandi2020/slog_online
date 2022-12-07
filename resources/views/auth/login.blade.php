@extends('layouts.guest')

@section('title', trans('auth.login'))

@section('content')
<div class="row col-sm-offset-4 col-sm-4 text-center" style="padding-top:5%">
    <section class="panel panel-default">
        <div class="panel-heading">
            <img style="margin: 2em auto 0.5em auto;" src="{{ url('imgs/logo-login.png') }}" alt="logo">
            <!-- <img src="https://sinergilogistik.com/wp-content/uploads/imam-cs.jpg" class="img-fluid" alt=""> -->
        </div>
        {{ Form::open(['route' => 'login', 'class' => 'panel-body']) }}
            <!-- <div class="lead text-muted">Hai, saya Imam Bjorka BonbonBJ69</div> -->
            <div class="lead text-muted">{{ trans('auth.welcome_please_login') }}</div>
            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                <input id="username" name="username" type="text" class="form-control" placeholder="Username" />
                @if ($errors->has('username'))
                <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" name="password" type="password" class="form-control" placeholder="Password" />
                @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>
            <div class="checkbox text-left">
                <label>
                    <input name="remember" type="checkbox" value="Remember Me">{{ trans('auth.remember_me') }}
                </label>
                {!! link_to_route('reset-request', trans('auth.forgot_password'),[],['class'=>'pull-right']) !!}
            </div>
            {!! Form::submit(trans('auth.login'), ['class'=>'btn btn-success btn-block']) !!}
            <hr>
            <div class="separator">
                <div>
                    <p>© 2019 All Rights Reserved.</p>
                    <p>{{ link_to(url('/'), config('app.company_name')) }}</p>
                </div>
            </div>
        {{ Form::close() }}
    </section>
</div>

@endsection
