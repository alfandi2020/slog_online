<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {!! Html::style('css/pdf.css') !!}
    <style>html {margin: 20px; }</style>
</head>
<body>
    <table border="1" class="labels-table" style="width: 775px">
        <thead>
            <tr>
                <th colspan="3" class="text-left"><h2>{{ trans('manifest.manifest') }} {{ $manifest->type() }}</h2></th>
                <th colspan="2">{{ trans('app.date') }} : {{ $manifest->created_at->format('Y-m-d') }}</th>
                <th colspan="3">{{ trans('app.created_by') }} : {{ $manifest->creator->name }}</th>
            </tr>
            <tr>
                <td colspan="8" class="text-center">{{ trans('manifest.number') }} :  <strong>{{ $manifest->number }}</strong></td>
            </tr>
            <tr>
                <td colspan="8" class="text-center">
                    {{ $manifest->customer->account_no }}
                    {{ $manifest->customer->code ? '(' . $manifest->customer->code . ')' : '' }}
                    {{ $manifest->customer->name }}
                </td>
            </tr>
            @if ($manifest->notes)
            <tr><td colspan="8">Catatan : {!! $manifest->notes !!}</td></tr>
            @endif
            <tr><td colspan="8" class="text-center">&nbsp;</td></tr>
            <tr>
                <th style="width:3%">{{ trans('app.table_no') }}</th>
                <th style="width:17%">{{ trans('receipt.number') }}</th>
                <th style="width:15%">{{ trans('receipt.customer') }}</th>
                <th style="width:10%">{{ trans('app.date') }}</th>
                <th style="width:10%">{{ trans('receipt.consignor') }}</th>
                <th style="width:15%">{{ trans('receipt.consignee') }}</th>
                <th style="width:15%">{{ trans('receipt.destination') }}</th>
                <th style="width:5%">{{ trans('service.service') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $itemCountTotal = 0;
            $itemWeightTotal = 0;
            ?>
            @foreach($manifest->receipts as $key => $receipt)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $receipt->number }}</td>
                <td>{{ $receipt->customer ? $receipt->customer->present()->numberName : '' }}</td>
                <td>{{ $receipt->pickup_time->format('Y-m-d') }}</td>
                <td>{{ $receipt->consignor['name'] }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td style="vertical-align:top">{{ $receipt->destination->name }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->service() }}</td>
            </tr>
            <?php
                $itemCountTotal += $receipt->items_count;
                $itemWeightTotal += $receipt->weight;
            ?>
            @endforeach
        </tbody>
    </table>
</body>
</html>