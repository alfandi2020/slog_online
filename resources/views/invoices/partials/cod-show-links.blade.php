{!! link_to_route('invoices.cod.pdf', trans('invoice.print'), [$invoice->id], ['class' => 'btn btn-info','target' => '_blank']) !!}
@can ('add-remove-receipt-of', $invoice)
    {{ link_to_route('invoices.cod.show', trans('manifest.add_remove_receipt'), [$invoice->id, 'action' => 'add_remove_receipt', '#add-remove-receipt'], ['class' => 'btn btn-default']) }}
@endcan
@can('send', $invoice)
    <?php $disabled = $invoice->receipts->isEmpty() ? 'disabled' : false ?>
    {!! FormField::formButton([
        'route'=> ['invoices.cod.deliver', $invoice->id],
        'onsubmit' => trans('invoice.send_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], trans('cod_invoice.send'), ['class'=>'btn btn-success', $disabled], ['deliver_invoice' => 'yes']) !!}
@endcan
@can('undeliver', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.cod.undeliver', $invoice->id],
        'onsubmit' => trans('invoice.take_back_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], trans('cod_invoice.take_back'), ['class'=>'btn btn-warning'], ['undeliver_invoice' => 'yes']) !!}
@endcan
@can('verify', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.cod.verify', $invoice->id],
        'onsubmit' => trans('invoice.verify_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], trans('invoice.verify'), ['class'=>'btn btn-success', 'id'=>'verify_cod_invoice']) !!}
@endcan
@can('edit', $invoice)
    {!! link_to_route('invoices.cod.edit', trans('invoice.edit'), [$invoice->id], ['class' => 'btn btn-warning']) !!}
@endif