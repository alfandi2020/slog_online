<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ Request::segment(3) == null ? 'active' : '' }}">
        {!! link_to_route('invoices.cod.show', trans('invoice.show'), $invoice) !!}
    </li>
    <li class="{{ Request::segment(3) == 'payments' ? 'active' : '' }}">
        {!! link_to_route('invoices.cod.payments.index', trans('invoice.payments').' ('.$invoice->payments->count().')', $invoice) !!}
    </li>
</ul>
<br>
