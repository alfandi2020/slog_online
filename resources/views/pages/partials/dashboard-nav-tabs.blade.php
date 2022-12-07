<!-- Nav tabs -->
<ul class="nav nav-tabs bar_tabs">
    <li class="{{ Request::get('tab') == 'receipt-monitor' || Request::get('tab') == null ? 'active' : '' }}">
        {{ link_to_route('home', 'Monitor Resi', ['tab' => 'receipt-monitor'] + Request::only('ym')) }}
    </li>
    <li class="{{ Request::get('tab') == 'cash' ? 'active' : '' }}">
        {{ link_to_route('home', trans('report.monitor.cash'), ['tab' => 'cash'] + Request::only('ym')) }}
    </li>
    <li class="{{ Request::get('tab') == 'credit' ? 'active' : '' }}">
        {{ link_to_route('home', trans('report.monitor.credit'), ['tab' => 'credit'] + Request::only('ym')) }}
    </li>
    <li class="{{ Request::get('tab') == 'cod' ? 'active' : '' }}">
        {{ link_to_route('home', trans('report.monitor.cod'), ['tab' => 'cod'] + Request::only('ym')) }}
    </li>
    <li class="{{ Request::get('tab') == 'receipt-retained' ? 'active' : '' }}">
        {{ link_to_route('home', 'Resi Tertahan', ['tab' => 'receipt-retained'] + Request::only('ym')) }}
    </li>
    <li class="{{ Request::get('tab') == 'receipt-uninvoiced-pod' ? 'active' : '' }}">
        {{ link_to_route('home', 'Resi Diterima tetapi Belum Invoice', ['tab' => 'receipt-uninvoiced-pod'] + Request::only('ym')) }}
    </li>
</ul>
<br>