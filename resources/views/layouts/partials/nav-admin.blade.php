<li>{!! html_link_to_route('receipts.drafts', trans('nav_menu.receipt_drafts'), [], ['icon' => 'edit']) !!}</li>
<li>{!! html_link_to_route('pickups.index', trans('pickup.pickup'), [], ['icon' => 'truck']) !!}</li>
<li>
    <a><i class="fa fa-money"></i> {{ trans('pod.pod_full') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{!! html_link_to_route('pods.by-manifest', trans('pod.by_manifest'), [], ['icon' => 'check-square-o']) !!}</li>
        <li>{!! html_link_to_route('pods.by-receipt', trans('pod.by_receipt'), [], ['icon' => 'check-square-o']) !!}</li>
    </ul>
</li>
<li>{!! html_link_to_route('receipts.returnings.index', trans('receipt.returnings_entry'), [], ['icon' => 'rotate-left']) !!}</li>
<li>
    <a><i class="fa fa-retweet"></i> {{ trans('nav_menu.manifests') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{!! html_link_to_route('manifests.index', trans('nav_menu.search_manifest'), [], ['icon' => 'search']) !!}</li>
        <li>{!! html_link_to_route('manifests.handovers.index', trans('manifest.handover'), [], ['icon' => 'upload']) !!}</li>
        <li>{!! html_link_to_route('manifests.deliveries.index', trans('manifest.delivery'), [], ['icon' => 'sign-out']) !!}</li>
        <li>{!! html_link_to_route('manifests.distributions.index', trans('manifest.distribution'), [], ['icon' => 'truck']) !!}</li>
        <li>{!! html_link_to_route('manifests.returns.index', trans('manifest.return'), [], ['icon' => 'rotate-left']) !!}</li>
        <li>{!! html_link_to_route('manifests.accountings.index', trans('manifest.accounting'), [], ['icon' => 'money']) !!}</li>
        <li>{!! html_link_to_route('manifests.problems.index', trans('manifest.problem'), [], ['icon' => 'exclamation-circle']) !!}</li>
    </ul>
</li>
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
<li>
    <a><i class="fa fa-database"></i> {{ trans('nav_menu.master_data') }} <span class="fa arrow"></span></a>

    <ul class="nav nav-second-level" role="menu">
        <li>{!! html_link_to_route('admin.regions.provinces', trans('nav_menu.regions')) !!}</li>
        <li>{!! html_link_to_route('admin.networks.index', trans('network.network')) !!}</li>
        <li>{!! html_link_to_route('admin.users.index', trans('user.list')) !!}</li>
        <li>{!! html_link_to_route('admin.package-types.index', trans('package_type.package_type')) !!}</li>
        <li>{!! html_link_to_route('admin.comodities.index', trans('comodity.comodity')) !!}</li>
        <li>{!! html_link_to_route('payment-methods.index', trans('payment_method.payment_method')) !!}</li>
        <li>{!! html_link_to_route('admin.backups.index', trans('backup.list')) !!}</li>
    </ul>
</li>
