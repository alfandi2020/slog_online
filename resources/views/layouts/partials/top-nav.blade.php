<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin: -15px -15px 15px -15px;">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="navbar-brand">@yield('title')</div>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">
    @if (Auth::check())
        <li>{!! html_link_to_route('dashboard.monitor.chart', trans('nav_menu.receipt_monitor_chart'), [], ['icon' => 'table']) !!}</li>
        <li>{!! html_link_to_route('pages.get-costs', trans('nav_menu.get_costs'), [], ['icon' => 'money']) !!}</li>
        @include('layouts.partials.top-notifications')
        <!-- /.dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i> <span class="hidden-xs">{{ auth()->user()->name }}</span> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li>{!! html_link_to_route('change-password', trans('auth.change_password'), [], ['icon' => 'lock']) !!}</li>
                <li>{!! html_link_to_route('logout', trans('auth.logout'), [], ['icon' => 'sign-out', 'id' => 'logout-button']) !!}</li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    @endif
    </ul>
    <!-- /.navbar-top-links -->
</nav>
