<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ (request('status') == 'proccess' || !in_array(request('status'), ['sent', 'paid', 'closed', 'problem'])) ? 'active' : '' }}">
        {!! link_to_route('reports.invoices', __('customer.proccess_invoice'), ['status' => 'proccess'], [
            'title' => __('customer.proccess_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'sent' ? 'active' : '' }}">
        {!! link_to_route('reports.invoices', __('customer.sent_invoice'), ['status' => 'sent'], [
            'title' => __('customer.sent_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'paid' ? 'active' : '' }}">
        {!! link_to_route('reports.invoices', __('customer.paid_invoice'), ['status' => 'paid'], [
            'title' => __('customer.paid_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'closed' ? 'active' : '' }}">
        {!! link_to_route('reports.invoices', __('customer.closed_invoice'), ['status' => 'closed'], [
            'title' => __('customer.closed_invoice')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'problem' ? 'active' : '' }}">
        {!! link_to_route('reports.invoices', __('customer.problem_invoice'), ['status' => 'problem'], [
            'title' => __('customer.problem_invoice')
        ]) !!}
    </li>
</ul>
<br>
