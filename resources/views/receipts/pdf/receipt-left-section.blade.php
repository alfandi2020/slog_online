<table class="full-bordered" style="font-size: 10px;">
    <tbody>
        <tr>
            <td style="height:69px;">
                {!! Html::image(url('imgs/logo2.png'), 'logo', ['style'=>'width: 70px;display:inline-block;margin-right:5px;margin-top: 7px']) !!}
                <div style="font-size: 9px;display:inline-block;width:174px;margin-top: 10px;margin-bottom: -10px">
                    <div class="lead strong" style="font-size: 11px">
                        {{ config('app.company_name') }}
                    </div>
                    {{ $receipt->network->address }}
                    Telp. {{ $receipt->network->phone }}<br>{{ Option::get('website_address') }}
                </div>
                <div style="font-size: 9.5px; text-align: center;">Izin Penyelenggaraan POS Nasional No. 1440 Tahun 2017</div>
            </td>
        </tr>
        <tr><th class="text-left">1. {{ trans('receipt.consignee') }}</th></tr>
        <tr>
            <td>
                <p class="strong">{{ $receipt->consignee['name'] }}</p>
                <p style="height:45px">
                    {{ $receipt->consignee['address'][1] }},
                    {{ $receipt->consignee['address'][2] }},
                    {{ $receipt->consignee['address'][3] }}<br>
                    {{ trans('address.postal_code') }} : {{ $receipt->consignee['postal_code'] }}
                </p>
            </td>
        </tr>
        <tr><td>Telp: {{ $receipt->consignee['phone'] }}</td></tr>
        <tr><th class="text-left">2. {{ trans('receipt.consignor') }}</th></tr>
        <tr>
            <td>
                <p class="strong">{{ $receipt->consignor['name'] }}</p>
                <p style="height:45px">
                    {{ $receipt->consignor['address'][1] }},
                    {{ $receipt->consignor['address'][2] }},
                    {{ $receipt->consignor['address'][3] }}<br>
                    {{ trans('address.postal_code') }} : {{ $receipt->consignor['postal_code'] }}
                </p>
            </td>
        </tr>
        <tr><td>Telp: {{ $receipt->consignor['phone'] }}</td></tr>
        <tr><td colspan="2"><b>{{ trans('receipt.reference_no') }}</b> : {{ $receipt->reference_no }}</td></tr>
        <tr><td colspan="2" style="height:32px">{{ trans('receipt.customer_invoice_no') }} : <br>{{ $receipt->customer_invoice_no }}</td></tr>
    </tbody>
</table>
