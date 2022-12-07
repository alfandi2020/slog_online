<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.consignee') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th class="col-xs-4">{{ trans('app.name') }}</th><td>{{ $receipt->consignee['name'] }}</td></tr>
            <tr>
                <th>{{ trans('address.address') }}</th>
                <td>
                    {{ $receipt->consignee['address'][1] }}
                    {{ $receipt->consignee['address'][2] }}<br>
                    {{ $receipt->consignee['address'][3] }}
                </td>
            </tr>
            <tr><th>{{ trans('address.postal_code') }}</th><td>{{ $receipt->consignee['postal_code'] }}</td></tr>
            <tr><th>{{ trans('contact.phone') }}</th><td>{{ $receipt->consignee['phone'] }}</td></tr>
            <tr><th>{{ trans('receipt.destination') }}</th><td>{{ $receipt->destinationName() }}</td></tr>
        </tbody>
    </table>
</div>