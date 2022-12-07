<?php
$notifications = auth()->user()->unreadNotifications;
?>
@if ($notifications->count())
<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-bell fa-fw"></i> {{ $notifications->count() }} <i class="fa fa-caret-down"></i>
    </a>
    <ul class="dropdown-menu dropdown-alerts">
        @foreach($notifications->take(5) as $notification)
        <li>@include('notifications.partials.' . getNotificationViewPart($notification->type))</li>
        @endforeach
        <li><div class="divider"></div></li>
        @if (($diff = $notifications->count() - 5) > 0)
        <li>{{ link_to_route('pages.notifications', 'Lihat '.$diff.' Notifikasi Lainnya') }}</li>
        @else
        <li>{{ link_to_route('pages.notifications', trans('nav_menu.view_all_notifications')) }}</li>
        @endif
    </ul>
    <!-- /.dropdown-alerts -->
</li>
@else
<li>
    {!! html_link_to_route('pages.notifications', '', [], [
        'icon' => 'bell',
        'class' => 'bell',
        'title' => trans('notification.empty_new'),
    ]) !!}
</li>
@endif