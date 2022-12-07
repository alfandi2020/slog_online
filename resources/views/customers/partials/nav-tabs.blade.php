<!-- Nav tabs -->
<ul class="nav nav-tabs bar_tabs">
    <li class="{{ Request::segment(4) == null ? 'active' : '' }}">
        {!! link_to_route('customers.show', trans('customer.show'), [$customer->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'rates' ? 'active' : '' }}">
        {!! link_to_route('customers.rates.index', trans('customer.rates'), [$customer->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'un-invoiced-receipts' ? 'active' : '' }}">
        {!! link_to_route('customers.un-invoiced-receipts', trans('receipt.un_invoiced'), [$customer->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'invoices' ? 'active' : '' }}">
        {!! link_to_route('customers.invoices', trans('customer.invoice_list'), [$customer->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'invoiced-receipts' ? 'active' : '' }}">
        {!! link_to_route('customers.invoiced-receipts', trans('receipt.invoiced'), [$customer->id]) !!}
    </li>
</ul>
<br>