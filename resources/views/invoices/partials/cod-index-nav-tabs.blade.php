<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ (request('status') == 'proccess' || !in_array(request('status'), ['sent', 'paid', 'closed'])) ? 'active' : '' }}">
        {!! link_to_route('invoices.cod.index', trans('cod_invoice.proccess_status'), ['status' => 'proccess'], [
            'title' => trans('cod_invoice.proccess')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'sent' ? 'active' : '' }}">
        {!! link_to_route('invoices.cod.index', trans('cod_invoice.sent_status'), ['status' => 'sent'], [
            'title' => trans('cod_invoice.sent')
        ]) !!}
    </li>
    <li class="{{ request('status') == 'closed' ? 'active' : '' }}">
        {!! link_to_route('invoices.cod.index', trans('cod_invoice.closed_status'), ['status' => 'closed'], [
            'title' => trans('cod_invoice.closed')
        ]) !!}
    </li>
</ul>
<br>