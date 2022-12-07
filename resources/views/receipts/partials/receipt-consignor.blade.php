<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.consignor') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th class="col-xs-4">{{ trans('app.name') }}</th><td>{{ $receipt->consignor['name'] }}</td></tr>
            <tr>
                <th>{{ trans('address.address') }}</th>
                <td>
                    {{ $receipt->consignor['address'][1] }}
                    {{ $receipt->consignor['address'][2] }}<br>
                    {{ $receipt->consignor['address'][3] }}
                </td>
            </tr>
            <tr><th>{{ trans('address.postal_code') }}</th><td>{{ $receipt->consignor['postal_code'] }}</td></tr>
            <tr><th>{{ trans('contact.phone') }}</th><td>{{ $receipt->consignor['phone'] }}</td></tr>
            <tr><th>{{ trans('receipt.origin') }}</th><td>{{ $receipt->originName() }}</td></tr>
        </tbody>
    </table>
</div>