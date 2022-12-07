<div class="panel panel-default table-responsive hidden-xs">
    <table class="table table-condensed table-bordered">
        <tr>
            <td class="col-xs-2 text-center">{{ trans('cod_invoice.number') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.total') }}</td>
            <td class="col-xs-2 text-center">{{ trans('app.status') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.creator') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.handler') }}</td>
        </tr>
        <tr>
            <td class="text-center lead" style="border-top: none;">{{ $invoice->number }}</td>
            <td class="text-center lead" style="border-top: none;">{{ formatRp($invoice->amount) }}</td>
            <td class="text-center lead" style="border-top: none;">{!! $invoice->present()->statusLabel !!}</td>
            <td class="text-center lead" style="border-top: none;">{{ $invoice->creator->name }}</td>
            <td class="text-center lead" style="border-top: none;">{{ $invoice->handler_id ? $invoice->handler->name : '-' }}</td>
        </tr>
    </table>
</div>
<div class="panel panel-default table-responsive hidden-xs">
    <table class="table table-condensed table-bordered">
        <tr>
            <td class="col-xs-2 text-center">{{ trans('invoice.receipts_count') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.date') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.sent_date') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.payment_date') }}</td>
            <td class="col-xs-2 text-center">{{ trans('invoice.verify_date') }}</td>
        </tr>
        <tr>
            <td class="text-center lead" style="border-top: none;">{{ $invoice->receipts->count() }}</td>
            <td class="text-center lead" style="border-top: none;">{{ dateId($invoice->date) }}</td>
            <td class="text-center lead" style="border-top: none;">{{ dateId($invoice->sent_date) }}</td>
            <td class="text-center lead" style="border-top: none;">{{ dateId($invoice->payment_date) }}</td>
            <td class="text-center lead" style="border-top: none;">{{ dateId($invoice->verify_date) }}</td>
        </tr>
    </table>
</div>

<ul class="list-group visible-xs">
    <li class="list-group-item">{{ trans('cod_invoice.number') }} <span class="pull-right">{{ $invoice->number }}</span></li>
    <li class="list-group-item">{{ trans('invoice.receipts_count') }} <span class="pull-right">{{ $invoice->receipts->count() }}</span></li>
    <li class="list-group-item">{{ trans('invoice.total') }} <span class="pull-right">{{ formatRp($invoice->amount) }}</span></li>
    <li class="list-group-item">{{ trans('app.status') }} <span class="pull-right">{!! $invoice->present()->statusLabel !!}</span></li>
    <li class="list-group-item">{{ trans('invoice.date') }} <span class="pull-right">{{ $invoice->date }}</span></li>
    <li class="list-group-item">{{ trans('invoice.sent_date') }} <span class="pull-right">{{ dateId($invoice->sent_date) }}</span></li>
    <li class="list-group-item">{{ trans('invoice.payment_date') }} <span class="pull-right">{{ $invoice->payment_date ?: '-' }}</span></li>
    <li class="list-group-item">{{ trans('invoice.verify_date') }} <span class="pull-right">{{ $invoice->verify_date ?: '-' }}</span></li>
    <li class="list-group-item">{{ trans('invoice.creator') }} <span class="pull-right">{{ $invoice->creator->name }}</span></li>
    <li class="list-group-item">{{ trans('invoice.handler') }} <span class="pull-right">{{ $invoice->handler_id ? $invoice->handler->name : '' }}</span></li>
</ul>