<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('invoice.progress') }}</h3></div>
    <table class="table table-condensed">
        <tr><th>{{ trans('invoice.created_date') }}</th><td class="text-center">{{ dateId($invoice->date) }}</td></tr>
        <tr><th>{{ trans('invoice.sent_date') }}</th><td class="text-center">{{ dateId($invoice->sent_date) }}</td></tr>
        <tr><th>{{ trans('invoice.received_date') }}</th><td class="text-center">{{ dateId($invoice->received_date) }}</td></tr>
        <tr><th>{{ trans('invoice.payment_date') }}</th><td class="text-center">{{ dateId($invoice->payment_date) }}</td></tr>
        <tr><th>{{ trans('invoice.verify_date') }}</th><td class="text-center">{{ dateId($invoice->verify_date) }}</td></tr>
    </table>
</div>
