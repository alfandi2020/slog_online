<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('pickup.pickup') }} No. {{ $pickup->number }}</title>
    {!! Html::style('css/pdf.css') !!}
    <style>
    html {margin: 20px; }

    table.labels-table th {
        border: 1px solid #666;
        padding: 5px 5px;
    }
    table .lead { font-size: 20px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <th class="text-left" style="width:33%"><h2>{{ trans('pickup.pickup') }}</h2></th>
            <th style="width:33%" class="text-center" rowspan="2">{!! Html::image(url('imgs/logo.png'), 'logo', ['style'=>'width:80px']) !!}</th>
            <th style="width:33%" class="text-center">{!! $pickup->barcode !!}</th>
        </tr>
        <tr>
            <td>
                <div>{{ trans('app.date') }} : <strong>{{ $pickup->created_at->format('Y-m-d') }}</strong></div>
                <div>{{ trans('app.created_by') }} : <strong>{{ $pickup->creator->name }}</strong></div>
            </td>
            <td class="text-center">
                <div>{{ trans('pickup.number') }} : <strong>{{ $pickup->number }}</strong></div>
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <h3>{{ trans('pickup.courier') }}</h3>
                <div style="margin:10px 0">{{ trans('app.name') }} : <strong>{{ $pickup->courier->name }}</strong></div>
                <div style="margin:10px 0">
                    {{ trans('pickup.delivery_unit') }} :
                    <strong>
                        {{ $pickup->deliveryUnit->plat_no }} - {{ $pickup->deliveryUnit->name }}
                    </strong>
                </div>
            </td>
            <td>
                <br>
                <h3>{{ trans('pickup.sent_at') }}</h3>
                <div style="margin:10px 0">{{ trans('app.date') }} : <strong>{{ $pickup->sent_at ? $pickup->sent_at->format('d-m-Y') : '___________________' }}</strong></div>
                <div style="margin:10px 0">{{ trans('app.time') }} &nbsp;&nbsp;&nbsp;: <strong>{{ $pickup->sent_at ? $pickup->sent_at->format('H:i') : '___________________' }}</strong></div>
            </td>
            <td>
                <br>
                <h3>{{ trans('pickup.returned_at') }}</h3>
                <div style="margin:10px 0">{{ trans('app.date') }} : <strong>{{ $pickup->returned_at ? $pickup->returned_at->format('d-m-Y') : '___________________' }}</strong></div>
                <div style="margin:10px 0">{{ trans('app.time') }} &nbsp;&nbsp;&nbsp;: <strong>{{ $pickup->returned_at ? $pickup->returned_at->format('H:i') : '___________________' }}</strong></div>
            </td>
        </tr>
    </table>
    <br>
    <table border="1" class="labels-table">
        <thead>
            <tr>
                <th style="width: 3%" class="text-center">{{ trans('app.table_no') }}</th>
                <th style="width: 25%" class="text-center">{{ trans('customer.customer') }}</th>
                <th style="width: 10%" class="text-center">{{ trans('pickup.receipts_count') }}</th>
                <th style="width: 10%" class="text-center">{{ trans('pickup.pcs_count') }}</th>
                <th style="width: 10%" class="text-center">{{ trans('pickup.items_count') }}</th>
                <th style="width: 10%" class="text-center">{{ trans('pickup.weight_total') }}</th>
                <th style="width: 17%">{{ trans('app.notes') }}</th>
                <th style="width: 15%" class="text-center">Tanda Tangan Customer</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $receiptsTotal = 0;
                $pcsTotal = 0;
                $itemsTotal = 0;
                $weightTotal = 0;
            @endphp
            @foreach($pickup->customers as $customerId => $pickupData)
            @php
                $customer = App\Entities\Customers\Customer::find($customerId);
            @endphp
            <tr>
                <td class="text-center text-middle" height="55px">{{ $no++ }}</td>
                <td class="text-middle">{{ $customer->name }}</td>
                <td class="text-center text-middle lead">{{ $receiptsCount = $pickupData['receipts_count'] }}</td>
                <td class="text-center text-middle lead">{{ $pcsCount = $pickupData['pcs_count'] }}</td>
                <td class="text-center text-middle lead">{{ $itemsCount = $pickupData['items_count'] }}</td>
                <td class="text-center text-middle lead">{{ $weight = $pickupData['weight_total'] }}</td>
                <td class="text-middle">{{ $pickupData['notes'] }}</td>
                <td>&nbsp;</td>
            </tr>
            @php
                $receiptsTotal += $receiptsCount;
                $pcsTotal += $pcsCount;
                $itemsTotal += $itemsCount;
                $weightTotal += $weight;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th height=50px" colspan="2" class="text-right">{{ trans('app.total') }}</th>
                <th class="text-center lead">{{ $receiptsTotal }}</th>
                <th class="text-center lead">{{ $pcsTotal }}</th>
                <th class="text-center lead">{{ $itemsTotal }}</th>
                <th class="text-center lead">{{ $weightTotal }}</th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <div><br>
        <table class="header-table" style="page-break-inside: avoid;">
            <tbody>
                @if ($pickup->notes)
                <tr><td colspan="3" style="line-height: 20px;padding-bottom: 10px">Catatan : {{ $pickup->notes }}</td></tr>
                @endif
                <tr>
                    <th style="width:30%"><h3>{{ trans('app.created_by') }}</h3></th>
                    <th style="width:40%">&nbsp;</th>
                    <th style="width:30%"><h3>{{ trans('pickup.courier') }}</h3></th>
                </tr>
                <tr>
                    <th style="height:120px">({{ $pickup->creator->name }})</th>
                    <th>&nbsp;</th>
                    <th>({{ $pickup->courier->name }})</th>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
