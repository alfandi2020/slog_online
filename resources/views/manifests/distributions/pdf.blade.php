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
            <th style="width:40%" rowspan="2">{!! Html::image(url('imgs/logo.png'), 'logo') !!}</th>
            <th style="width:30%" class="text-center">{!! generateQrCode($manifest->number, 70) !!}</th>
        </tr>
        <tr>
            <td>
                <div>{{ trans('app.date') }} : <strong>{{ $manifest->created_at->format('Y-m-d') }}</strong></div>
                <div>{{ trans('app.created_by') }} : <strong>{{ $manifest->present()->creatorName }}</strong></div>
            </td>
            <td class="text-center">
                <div>{{ trans('manifest.number') }} : <strong>{{ $manifest->number }}</strong></div>
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <h3>{{ trans('manifest.courier') }}</h3>
                <div>{{ trans('app.name') }} : <strong>{{ $manifest->present()->courierName }}</strong></div>
                <div>{{ trans('manifest.delivery_unit') }} : <strong>{{ $manifest->present()->deliveryUnitName }}</strong></div>
            </td>
            <td>
                <br>
                <h3>{{ trans('manifest.distributions.deliver_at') }}</h3>
                <div>{{ trans('app.date') }} : <strong>{{ $manifest->deliver_at ? $manifest->deliver_at->format('d-m-Y') : '___________________' }}</strong></div>
                <div>{{ trans('app.time') }} &nbsp;&nbsp;&nbsp;: <strong>{{ $manifest->deliver_at ? $manifest->deliver_at->format('H:i') : '___________________' }}</strong></div>
            </td>
            <td>
                <br>
                <h3>{{ trans('manifest.distributions.received_at') }}</h3>
                <div>{{ trans('app.date') }} : <strong>{{ $manifest->received_at ? $manifest->received_at->format('d-m-Y') : '___________________' }}</strong></div>
                <div>{{ trans('app.time') }} &nbsp;&nbsp;&nbsp;: <strong>{{ $manifest->received_at ? $manifest->received_at->format('H:i') : '___________________' }}</strong></div>
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
                <th style="width:15%">{{ trans('receipt.consignor') }}</th>
                <th style="width:15%">{{ trans('receipt.consignee') }}</th>
                <th style="width:17%">{{ trans('receipt.destination') }}</th>
                <th style="width:8%">Dus / Koli</th>
                <th style="width:7%">{{ trans('receipt.weight') }}</th>
                <th style="width:7%">{{ trans('service.service') }}</th>
                <th style="width:13%">{{ trans('receipt.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $pcsCountTotal = 0;
                $itemCountTotal = 0;
                $itemWeightTotal = 0;
                $codTotal = 0;
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
                <td style="vertical-align:top" class="text-center">{{ $receipt->items_count }} / {{ $receipt->pcs_count }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->weight }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->service() }}</td>
                <td>
                    @if ($receipt->payment_type_id == '3')
                    <small>COD :<br><div class="text-right">{{ formatRp($receipt->bill_amount) }}</div></small><br>
                    <?php $codTotal += $receipt->bill_amount;?>
                    @endif
                    <div style="font-size: 10px">{!! optional($receipt->customer)->pod_checklist_display !!}</div>
                    {{ $receipt->notes }}
                </td>
            </tr>
            <?php
                $pcsCountTotal += $receipt->pcs_count;
                $itemCountTotal += $receipt->items_count;
                $itemWeightTotal += $receipt->weight;
            ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">Jumlah</th>
                <th>{{ $itemCountTotal }} / {{ $pcsCountTotal }}</th>
                <th>{{ $itemWeightTotal }}</th>
                <th>&nbsp;</th>
                <td>
                    @if ($codTotal)
                    <small>
                        COD Total:
                        <div class="text-right strong">{{ formatRp($codTotal) }}</div>
                    </small>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
    <div><br>
        <table class="header-table" style="page-break-inside: avoid;">
            <tbody>
                <tr>
                    <th style="width:30%"><h2>{{ trans('manifest.checker') }}</h2></th>
                    <th style="width:40%"><h2>{{ trans('manifest.coordinator') }}</h2></th>
                    <th style="width:30%"><h2>{{ trans('manifest.courier') }}</h2></th>
                </tr>
                <tr>
                    <th style="height:100px">(___________________)</th>
                    <th>(___________________)</th>
                    <th>({{ $manifest->present()->courierName }})</th>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>