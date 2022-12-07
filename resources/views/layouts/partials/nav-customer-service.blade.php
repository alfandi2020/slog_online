<li>{!! html_link_to_route('invoices.cod.index', trans('cod_invoice.list'), [], ['icon' => 'money']) !!}</li>
<li>
    <a><i class="fa fa-money"></i> {{ trans('pod.pod_full') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{!! html_link_to_route('pods.by-manifest', trans('pod.by_manifest'), [], ['icon' => 'check-square-o']) !!}</li>
        <li>{!! html_link_to_route('pods.by-receipt', trans('pod.by_receipt'), [], ['icon' => 'check-square-o']) !!}</li>
    </ul>
</li>
<li>{!! html_link_to_route('receipts.returnings.index', trans('receipt.returnings_entry'), [], ['icon' => 'rotate-left']) !!}</li>
<li>
    <a><i class="fa fa-money"></i> {{ trans('nav_menu.manifests') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{!! html_link_to_route('manifests.distributions.index', trans('manifest.distribution'), [], ['icon' => 'truck']) !!}</li>
        <li>{!! html_link_to_route('manifests.returns.index', trans('manifest.return'), [], ['icon' => 'rotate-right']) !!}</li>
        <li>{!! html_link_to_route('manifests.accountings.index', trans('manifest.accounting'), [], ['icon' => 'money']) !!}</li>
        <li>{!! html_link_to_route('manifests.problems.index', trans('manifest.problem'), [], ['icon' => 'exclamation-circle']) !!}</li>
    </ul>
</li>

<li>
    <a><i class="fa fa-table"></i> {{ trans('nav_menu.receipt_reports') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level" role="menu">
        <li>{{ link_to_route('reports.receipt.export', trans('report.receipt.export')) }}</li>
        <li>{{ link_to_route('reports.receipt.unreturned', trans('report.receipt.unreturned')) }}</li>
        <li>{{ link_to_route('reports.receipt.retained', trans('report.receipt.retained')) }}</li>
        <li>{{ link_to_route('reports.receipt.late', trans('report.receipt.late')) }}</li>
    </ul>
</li>
