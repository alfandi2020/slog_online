<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('manifest.print_title', ['number' => $manifest->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
    <style>html {margin: 20px; }</style>
</head>
<body>
    <table class="header-table">
        <tr>
            <th class="text-left" style="width:30%" rowspan="2"><h2>{{ trans('manifest.manifest') }} {{ $manifest->type() }}</h2></th>
            <th style="width:35%" rowspan="3">{!! Html::image(url('imgs/logo.png'), 'logo') !!}</th>
            <th style="width:30%">{!! generateQrCode($manifest->number, 70) !!}</th>
        </tr>
        <tr><th><h4>{{ trans('manifest.number') }} : {{ $manifest->number }}</h4></th></tr>
        <tr>
            <th class="text-left"><h4>{{ trans('app.created_by') }} : {{ $manifest->creator->name }}</h4></th>
            <th><h4>{{ trans('app.date') }} : {{ $manifest->created_at->format('Y-m-d') }}</h4></th>
        </tr>
    </table>
    <p>{!! $manifest->notes ? '<br>Catatan : ' . $manifest->notes : '' !!}</p>
    <br>
    <table border="1" class="labels-table">
        <thead>
            <tr>
                <th style="width:3%">{{ trans('app.table_no') }}</th>
                <th style="width:17%">{{ trans('receipt.number') }}</th>
                <th style="width:15%">{{ trans('receipt.consignor') }}</th>
                <th style="width:15%">{{ trans('receipt.consignee') }}</th>
                <th style="width:16%">{{ trans('receipt.destination') }}</th>
                <th style="width:7%">{{ trans('receipt.items_count') }}</th>
                <th style="width:7%">{{ trans('receipt.weight') }}</th>
                <th style="width:7%">{{ trans('service.service') }}</th>
                <th style="width:13%">{{ trans('receipt.notes') }}</th>
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
                <td>{{ $receipt->number }} <br>{{ $receipt->packType->name }}</td>
                <td>
                    <p>{{ $receipt->consignor['name'] }}</p>
                    <p style="font-size:10px">
                        {{ $receipt->consignor['address'][1] }}
                        {{ $receipt->consignor['address'][2] }}<br>
                        {{ $receipt->consignor['address'][3] }}
                    </p>
                </td>
                <td>
                    <p>{{ $receipt->consignee['name'] }}</p>
                    <p style="font-size:10px">
                        {{ $receipt->consignee['address'][1] }}
                        {{ $receipt->consignee['address'][2] }}<br>
                        {{ $receipt->consignee['address'][3] }}
                    </p>
                </td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->destination->name }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->items_count }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->weight }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->service() }}</td>
                <td>{{ $receipt->notes }}</td>
            </tr>
            <?php
                $itemCountTotal += $receipt->items_count;
                $itemWeightTotal += $receipt->weight;
            ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">Jumlah</th>
                <th>{{ $itemCountTotal }}</th>
                <th>{{ $itemWeightTotal }}</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <div>
        <table class="header-table" style="page-break-inside: avoid;">
            <tbody>
                <tr>
                    <th style="width:25%"><h2>{{ trans('receipt.consignee') }}</h2></th>
                    <th style="width:50%" rowspan="2">&nbsp;</th>
                    <th style="width:25%"><h2>{{ trans('receipt.consignor') }}</h2></th>
                </tr>
                <tr>
                    <th style="height:100px">(___________________)</th>
                    <th>({{ $manifest->creator->name }})</th>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>