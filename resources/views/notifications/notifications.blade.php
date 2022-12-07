@extends('layouts.app')

@section('title', trans('notification.list'))

@section('content')
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ trans('notification.list') }} <span class="pull-right">{{ auth()->user()->unreadNotifications->count() }} belum terbaca</span></h3>
            </div>
            <table class="table">
                @foreach($notifications as $notification)
                <tr class="{{ $notification->read_at ? '' : 'bg-warning' }}">
                    <td>
                        @include('notifications.partials.' . getNotificationViewPart($notification->type), ['isPageContent' => true])
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        {{ $notifications->appends(Request::except('page'))->render() }}
    </div>
</div>
@endsection
