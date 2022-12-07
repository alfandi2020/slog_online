<li>
    <a><i class="fa fa-money"></i> {{ trans('nav_menu.accounting') }} <span class="fa arrow"></span></a>

    <ul class="nav nav-second-level" role="menu">
        <li>{{ link_to_route('rates.index', trans('rate.list')) }}</li>
        <li>{{ link_to_route('customers.index', trans('customer.list')) }}</li>
        <li>{{ link_to_route('rates.exports.index', trans('rate.list_export')) }}</li>
    </ul>
</li>
<li>
    <a><i class="fa fa-area-chart"></i> {{ trans('nav_menu.reports') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>
            <a><i class="fa fa-line-chart"></i> {{ trans('nav_menu.omzet') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.omzet-recap.monthly', trans('report.omzet-recap.monthly'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.omzet-recap.yearly', trans('report.omzet-recap.yearly'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.omzet.daily', trans('report.omzet.daily'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.omzet.monthly', trans('report.omzet.monthly'), [], ['icon' => 'line-chart']) !!}</li>
                <li>{!! html_link_to_route('reports.omzet.yearly', trans('report.omzet.yearly'), [], ['icon' => 'line-chart']) !!}</li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-table"></i> {{ trans('report.time_series.time_series') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.time_series.omzet', trans('report.time_series.omzet'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.time_series.invoice', trans('report.time_series.invoice'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.time_series.closed_invoice', trans('report.time_series.closed_invoice'), [], ['icon' => 'table']) !!}</li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-users"></i> {{ trans('nav_menu.network_omzet') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.network-omzet.monthly', trans('report.network_omzet.monthly'), [], ['icon' => 'line-chart']) !!}</li>
                <li>{!! html_link_to_route('reports.network-omzet.yearly', trans('report.network_omzet.yearly'), [], ['icon' => 'line-chart']) !!}</li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-users"></i> {{ trans('nav_menu.comodity_omzet') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.comodity-omzet.monthly', trans('report.comodity_omzet.monthly'), [], ['icon' => 'line-chart']) !!}</li>
                <li>{!! html_link_to_route('reports.comodity-omzet.yearly', trans('report.comodity_omzet.yearly'), [], ['icon' => 'line-chart']) !!}</li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-retweet"></i> {{ trans('nav_menu.manifests') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.manifests.distributions', trans('report.manifest.distributions'), [], ['icon' => 'truck']) !!}</li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-table"></i> {{ trans('nav_menu.receipts') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.receipt.export', trans('report.receipt.export'), [], ['icon' => 'external-link']) !!}</li>
                <li>{!! html_link_to_route('reports.receipt.returned', trans('report.receipt.returned'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.receipt.unreturned', trans('report.receipt.unreturned'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.receipt.retained', trans('report.receipt.retained'), [], ['icon' => 'table']) !!}</li>
                <li>{!! html_link_to_route('reports.receipt.late', trans('report.receipt.late'), [], ['icon' => 'table']) !!}</li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-money"></i> {{ trans('report.invoice') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level" role="menu">
                <li>{!! html_link_to_route('reports.invoices', trans('report.invoice'), [], ['icon' => 'money']) !!}</li>
                <li>{!! html_link_to_route('reports.invoices.receivables', trans('report.account_receivables'), [], ['icon' => 'money']) !!}</li>
            </ul>
        </li>
    </ul>
</li>
<li>
    <a><i class="fa fa-money"></i> {{ trans('nav_menu.invoices') }} <span class="fa arrow"></span></a>

    <ul class="nav nav-second-level" role="menu">
        <li>{{ link_to_route('invoices.search', trans('invoice.search')) }}</li>
        <li>{{ link_to_route('invoices.customer-list', trans('customer.un_invoiced')) }}</li>
        <li>{{ link_to_route('invoices.index', trans('invoice.list')) }}</li>
        <li>{{ link_to_route('invoices.cash.index', trans('cash_invoice.list')) }}</li>
        <li>{{ link_to_route('invoices.cod.index', trans('cod_invoice.list')) }}</li>
    </ul>
</li>
