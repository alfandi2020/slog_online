<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ Request::segment(4) == null ? 'active' : '' }}">
        {!! link_to_route('invoices.show', trans('invoice.show'), $invoice) !!}
    </li>
    <li class="{{ Request::segment(4) == 'receipts' ? 'active' : '' }}">
        {!! link_to_route('invoices.receipts.index', trans('invoice.receipts').' ('.$invoice->receipts->count().')', $invoice) !!}
    </li>
</ul>
<br>
