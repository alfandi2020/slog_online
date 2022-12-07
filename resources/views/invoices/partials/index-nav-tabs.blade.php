<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ (request('status') == 'proccess' || !in_array(request('status'), ['sent', 'paid', 'closed'])) ? 'active' : '' }}">
        {!! link_to_route('invoices.index', trans('customer.proccess_invoice'), ['status' => 'proccess'], [
            'title' => trans('customer.proccess_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'sent' ? 'active' : '' }}">
        {!! link_to_route('invoices.index', trans('customer.sent_invoice'), ['status' => 'sent'], [
            'title' => trans('customer.sent_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'paid' ? 'active' : '' }}">
        {!! link_to_route('invoices.index', trans('customer.paid_invoice'), ['status' => 'paid'], [
            'title' => trans('customer.paid_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'closed' ? 'active' : '' }}">
        {!! link_to_route('invoices.index', trans('customer.closed_invoice'), ['status' => 'closed'], [
            'title' => trans('customer.closed_invoice')
        ]) !!}
    </li>
</ul>
<br>