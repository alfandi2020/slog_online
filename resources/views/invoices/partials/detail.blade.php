<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('invoice.show') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th class="col-xs-4">{{ trans('invoice.number') }}</th><td>{{ $invoice->number }}</td></tr>
            <tr><th>{{ trans('invoice.periode') }}</th><td>{{ $invoice->periode }}</td></tr>
            <tr><th>{{ trans('app.status') }}</th><td>{!! $invoice->present()->statusLabel !!}</td></tr>
            <tr><th>{{ trans('invoice.receipts_count') }}</th><td>{{ $invoice->receipts->count() }}</td></tr>
            <tr>
                <th>{{ trans('invoice.customer') }}</th>
                <td>
                    <p>
                        {{ $invoice->customer->present()->nameLink() }} <br>
                        {{ $invoice->customer->present()->addresses }}
                    </p>
                    UP. <b>{{ $invoice->customer->pic['name'] }}</b>
                </td>
            </tr>
            <tr><th>{{ trans('app.date') }}</th><td>{{ dateId($invoice->date) }}</td></tr>
            <tr><th>{{ trans('invoice.end_date') }}</th><td>{{ dateId($invoice->end_date) }}</td></tr>
            <tr><th>{{ trans('app.created_at') }}</th><td>{{ $invoice->created_at }}</td></tr>
            <tr><th>{{ trans('invoice.creator') }}</th><td>{{ $invoice->creator->name }}</td></tr>
            <tr><th>{{ trans('app.notes') }}</th><td>{{ $invoice->notes }}</td></tr>
        </tbody>
    </table>
</div>
