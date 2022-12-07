<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('manifest.print_title', ['type' => $manifest->type(), 'number' => $manifest->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
    <style>html {margin: 20px; }</style>
</head>
<body>
    <table class="header-table">
        <tr>
            <th class="text-left" style="width:30%"><h2>{{ trans('manifest.manifest') }} {{ $manifest->type() }}</h2></th>
            <th style="width:35%" rowspan="3">{!! Html::image(url('imgs/logo.png'), 'logo') !!}</th>
            <th style="width:30%" class="text-center">{!! generateQrCode($manifest->number, 70) !!}</th>
        </tr>
        <tr>
            <td rowspan="2">
                <div>{{ trans('app.date') }} : {{ $manifest->created_at->format('Y-m-d') }}</div>
                <div>{{ trans('app.created_by') }} : {{ $manifest->creator->name }}</div>
            </td>
            <td class="text-center">
                <div>{{ trans('manifest.number') }} :  <strong>{{ $manifest->number }}</strong></div>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <br>
                <div>
                    {{ $manifest->customer->account_no }}
                    {{ $manifest->customer->code ? '(' . $manifest->customer->code . ')' : '' }} <br>
                    {{ $manifest->customer->name }}
                </div>
            </td>
        </tr>
    </table>
    <p>{!! $manifest->notes ? '<br>Catatan : ' . $manifest->notes : '' !!}</p>
    <br>
    <table border="1" class="labels-table">
        <thead>
            <tr>
                <th style="width:3%">{{ trans('app.table_no') }}</th>
                <th style="width:17%">{{ trans('receipt.number') }}</th>
                <th style="width:20%">{{ trans('receipt.customer') }}</th>
                <th style="width:15%">{{ trans('receipt.consignor') }}</th>
                <th style="width:15%">{{ trans('receipt.consignee') }}</th>
                <th style="width:20%">{{ trans('receipt.destination') }}</th>
                <th style="width:10%">{{ trans('service.service') }}</th>
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
                <td>{{ $receipt->consignor['name'] }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->destination->name }}</td>
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