{{-- Verify Invoice if it was already paid --}}
@can('verify', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.verify', $invoice],
        'onsubmit' => __('invoice.verify_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], __('invoice.verify'), ['class' => 'btn btn-success']) !!}
@endcan

{{-- Set invoice as paid --}}
@can('set-paid', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.set-paid', $invoice],
        'onsubmit' => __('invoice.paid_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], __('invoice.set_paid'), ['class' => 'btn btn-success']) !!}
@endcan

{{-- Set paid invoice as unpaid --}}
@can('set-unpaid', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.set-unpaid', $invoice],
        'method' => 'patch',
    ], __('invoice.set_unpaid'), ['class' => 'btn btn-warning']) !!}
@endcan

{{-- Export Invoice --}}
{!! link_to_route('invoices.pdf', __('invoice.print'), [$invoice], ['class' => 'btn btn-info','target' => '_blank']) !!}
{!! link_to_route('invoices.export-xls', __('invoice.export_xls'), [$invoice], ['class' => 'btn btn-info']) !!}

{{-- Take Invoice back from sent status --}}
@can ('take-back', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.undeliver', $invoice],
        'onsubmit' => __('invoice.take_back_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], __('invoice.take_back'), ['class' => 'btn btn-warning'], ['undeliver_invoice' => 'yes']) !!}
@endcan

{{-- Send invoice --}}
@can ('send', $invoice)
    <?php $disabled = $invoice->receipts->isEmpty() ? 'disabled' : false?>
    {!! FormField::formButton([
        'route'=> ['invoices.deliver', $invoice],
        'onsubmit' => __('invoice.send_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], __('invoice.send'), ['class' => 'btn btn-success', $disabled], ['deliver_invoice' => 'yes']) !!}
@endcan

{{-- Set invoice as problem --}}
@can('set-problem', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.set-problem', $invoice],
        'onsubmit' => __('invoice.problem_confirm', ['number' => $invoice->number]),
        'method' => 'patch',
    ], __('invoice.set_problem'), ['class' => 'btn btn-danger']) !!}
@endcan

{{-- Unset invoice from problem --}}
@can('unset-problem', $invoice)
    {!! FormField::formButton([
        'route'=> ['invoices.unset-problem', $invoice],
        'method' => 'delete',
    ], __('invoice.unset_problem'), ['class' => 'btn btn-warning']) !!}
@endcan

{{-- Edit Invoice --}}
@can('edit', $invoice)
{!! link_to_route('invoices.edit', __('invoice.edit'), [$invoice], ['class' => 'btn btn-warning']) !!}
@endif

{{-- Back to Invoice List based on current invoice status --}}
{!! link_to_route('invoices.index', __('invoice.back_to_index'), [
    'status' => $invoice->present()->status['code']
], ['class' => 'btn btn-default']) !!}