<div class="navbar-default sidebar hidden-print" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li class="sidebar-search text-center">
                <a href="{{ route('home') }}">
                    {{ Html::image(url('imgs/logo.png'), trans('nav_menu.home') . ' | ' . config('app.name'), [
                        'title' => trans('nav_menu.home') . ' | ' . config('app.name'), 'style' => 'width: 100px;',
                    ]) }}
                </a>
            </li>
            <li>{!! html_link_to_route('home', trans('nav_menu.home'), [], ['icon' => 'home']) !!}</li>
            <li>
                <a><i class="fa fa-table"></i> {{ trans('nav_menu.receipts') }} <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level" role="menu">
                    <li>{!! html_link_to_route('receipts.search', trans('nav_menu.search_receipt'), [], ['icon' => 'search']) !!}</li>
                    <li>{!! html_link_to_route('receipts.index', trans('receipt.list'), [], ['icon' => 'newspaper-o']) !!}</li>
                    <li>{!! html_link_to_route('receipts.retained', trans('report.receipt.retained'), [], ['icon' => 'list']) !!}</li>
                </ul>
            </li>
            @includeWhen(Auth::user()->isAdmin(), 'layouts.partials.nav-admin')
            @includeWhen(Auth::user()->isAccounting(), 'layouts.partials.nav-accounting')
            @includeWhen(Auth::user()->isSalesCounter(), 'layouts.partials.nav-sales-counter')
            @includeWhen(Auth::user()->isWarehouse(), 'layouts.partials.nav-warehouse')
            @includeWhen(Auth::user()->isCustomerService(), 'layouts.partials.nav-customer-service')
            @includeWhen(Auth::user()->isCashier(), 'layouts.partials.nav-cashier')
            @includeWhen(Auth::user()->isBranchHead(), 'layouts.partials.nav-branch-head')
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->
