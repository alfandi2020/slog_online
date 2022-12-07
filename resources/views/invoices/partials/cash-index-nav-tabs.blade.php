<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ (request('status') == 'proccess' || !in_array(request('status'), ['sent', 'paid', 'closed'])) ? 'active' : '' }}">
        {!! link_to_route('invoices.cash.index', trans('cash_invoice.proccess_status'), ['status' => 'proccess'], [
            'title' => trans('cash_invoice.proccess')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'sent' ? 'active' : '' }}">
        {!! link_to_route('invoices.cash.index', trans('cash_invoice.sent_status'), ['status' => 'sent'], [
            'title' => trans('cash_invoice.sent')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'closed' ? 'active' : '' }}">
        {!! link_to_route('invoices.cash.index', trans('cash_invoice.closed_status'), ['status' => 'closed'], [
            'title' => trans('cash_invoice.closed')
        ]) !!}
    </li>
</ul>
<br>