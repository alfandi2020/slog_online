<div class="panel panel-default">
    <div class="panel-heading">
        @can('update-delivery-info', $invoice)
        <div class="pull-right"><a data-toggle="modal" style="cursor: pointer;" data-target="#invoiceDelivery">{{ trans('app.edit') }}</a></div>
        @endcan
        <h3 class="panel-title">{{ trans('invoice.delivery_info') }}</h3>
    </div>
    <table class="table table-condensed">
        <tbody>
            <tr><th>{{ trans('invoice.creator') }}</th><td>{{ $invoice->creator->name }}</td></tr>
            <tr><th>{{ trans('invoice.consignor') }}</th><td>{{ $invoice->delivery_info['consignor'] }}</td></tr>
            <tr><th>{{ trans('invoice.consignee') }}</th><td>{{ $invoice->delivery_info['consignee'] }}</td></tr>
        </tbody>
    </table>
</div>
@can('update-delivery-info', $invoice)
<div class="modal fade" id="invoiceDelivery" role="dialog" aria-labelledby="invoiceDeliveryLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="invoiceDeliveryLabel">{{ trans('invoice.delivery_info') }}</h4>
            </div>
            {{ Form::open(['route' => ['invoices.store-delivery-info', $invoice], 'method' => 'patch']) }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        {!! FormField::textDisplay(trans('invoice.creator'), $invoice->creator->name) !!}
                    </div>
                    <div class="col-md-6">
                        {!! FormField::text('consignor', [
                            'label' => trans('invoice.consignor'),
                            'value' => request('consignor', $invoice->delivery_info['consignor']),
                        ]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! FormField::text('consignee', [
                            'label' => trans('invoice.consignee'),
                            'value' => request('consignee', $invoice->delivery_info['consignee']),
                        ]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! FormField::text('received_date', [
                            'label' => trans('invoice.received_date'),
                            'class' => 'date-select',
                            'value' => request('received_date', $invoice->received_date),
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {{ Form::submit(trans('invoice.update_delivery'), ['class' => 'btn btn-warning']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endcan
