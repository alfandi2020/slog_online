<li>{!! html_link_to_route('manifests.accountings.index', trans('manifest.accountings.list'), [], ['icon' => 'money']) !!}</li>
<li>{!! html_link_to_route('manifests.problems.index', trans('manifest.manifest') . ' ' . trans('manifest.problem'), [], ['icon' => 'exclamation-circle']) !!}</li>
<li>
    <a><i class="fa fa-money"></i> {{ trans('nav_menu.invoices') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{{ link_to_route('invoices.search', trans('invoice.search')) }}</li>
        <li>{{ link_to_route('invoices.customer-list', trans('customer.un_invoiced')) }}</li>
        <li>{{ link_to_route('invoices.index', trans('invoice.list')) }}</li>
    </ul>
</li>
<li>{!! html_link_to_route('rates.index', trans('rate.list'), [], ['icon' => 'table']) !!}</li>
<li>{!! html_link_to_route('customers.index', trans('customer.list'), [], ['icon' => 'users']) !!}</li>
<li>
    <a><i class="fa fa-table"></i> {{ trans('nav_menu.receipt_reports') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{{ link_to_route('reports.receipt.export', trans('report.receipt.export')) }}</li>
        <li>{{ link_to_route('reports.receipt.unreturned', trans('report.receipt.unreturned')) }}</li>
        <li>{{ link_to_route('reports.receipt.retained', trans('report.receipt.retained')) }}</li>
        <li>{{ link_to_route('reports.receipt.late', trans('report.receipt.late')) }}</li>
    </ul>
</li>