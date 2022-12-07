<li>{!! html_link_to_route('invoices.search', trans('invoice.search'), [], ['icon' => 'search']) !!}</li>
<li>{!! html_link_to_route('invoices.index', trans('invoice.list'), [], ['icon' => 'money']) !!}</li>
<li>{!! html_link_to_route('invoices.cash.index', trans('cash_invoice.list'), [], ['icon' => 'money']) !!}</li>
<li>{!! html_link_to_route('invoices.cod.index', trans('cod_invoice.list'), [], ['icon' => 'money']) !!}</li>
<li>{!! html_link_to_route('reports.receipt.export', trans('report.receipt.export'), [], ['icon' => 'external-link']) !!}</li>
<li>
    <a><i class="fa fa-money"></i> {{ trans('report.invoice') }} <span class="fa arrow"></span></a>
    <ul class="nav nav-third-level" role="menu">
        <li>{!! html_link_to_route('reports.invoices', trans('report.invoice'), ['status' => 'sent'], ['icon' => 'money']) !!}</li>
        <li>{!! html_link_to_route('reports.invoices.receivables', trans('report.account_receivables'), [], ['icon' => 'money']) !!}</li>
    </ul>
</li>
