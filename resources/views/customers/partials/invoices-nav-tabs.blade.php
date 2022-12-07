<!-- Nav tabs -->
<ul class="nav nav-tabs underlined-tabs">
    <li class="{{ Request::segment(4) == 'invoices' && (request('status') == 'proccess' || request('status') == null) ? 'active' : '' }}">
        {!! link_to_route('customers.invoices', trans('customer.proccess_invoice'), [$customer->id, 'status' => 'proccess'], [
            'title' => trans('customer.proccess_invoice')
        ]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'invoices' && request('status') == 'sent' ? 'active' : '' }}">
        {!! link_to_route('customers.invoices', trans('customer.sent_invoice'), [$customer->id, 'status' => 'sent'], [
            'title' => trans('customer.sent_invoice')
        ]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'invoices' && request('status') == 'paid' ? 'active' : '' }}">
        {!! link_to_route('customers.invoices', trans('customer.paid_invoice'), [$customer->id, 'status' => 'paid'], [
            'title' => trans('customer.paid_invoice')
        ]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'invoices' && request('status') == 'closed' ? 'active' : '' }}">
        {!! link_to_route('customers.invoices', trans('customer.closed_invoice'), [$customer->id, 'status' => 'closed'], [
            'title' => trans('customer.closed_invoice')
        ]) !!}
    </li>
</ul>
<br>