<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $manifest->number }}</title>
    {!! Html::style('css/pdf.css') !!}
    <style>html {margin: 20px; }</style>
</head>
<body>
    <table class="label-table">
        <tr>
            <th class="text-left" style="width:30%"><h2>{{ __('manifest.manifest') }} {{ $manifest->type() }}</h2></th>
            <th style="width:40%" rowspan="2">&nbsp;</th>
            <th style="width:30%">&nbsp;</th>
        </tr>
        <tr>
            <td>
                <div>{{ __('app.date') }} : <strong>{{ $manifest->created_at->format('Y-m-d') }}</strong></div>
                <div>{{ __('app.created_by') }} : <strong>{{ $manifest->present()->creatorName }}</strong></div>
            </td>
            <td class="text-center">
                <div>{{ __('manifest.number') }} : <strong>{{ $manifest->number }}</strong></div>
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <h3>{{ __('manifest.courier') }}</h3>
                <div>{{ __('app.name') }} : <strong>{{ $manifest->present()->courierName }}</strong></div>
                <div>{{ __('manifest.delivery_unit') }} : <strong>{{ $manifest->present()->deliveryUnitName }}</strong></div>
            </td>
            <td>
                <br>
                <h3>{{ __('manifest.distributions.deliver_at') }}</h3>
                <div>{{ __('app.date') }} : <strong>{{ $manifest->deliver_at ? $manifest->deliver_at->format('d-m-Y') : '___________________' }}</strong></div>
                <div>{{ __('app.time') }} &nbsp;&nbsp;&nbsp;: <strong>{{ $manifest->deliver_at ? $manifest->deliver_at->format('H:i') : '___________________' }}</strong></div>
            </td>
            <td>
                <br>
                <h3>{{ __('manifest.distributions.received_at') }}</h3>
                <div>{{ __('app.date') }} : <strong>{{ $manifest->received_at ? $manifest->received_at->format('d-m-Y') : '___________________' }}</strong></div>
                <div>{{ __('app.time') }} &nbsp;&nbsp;&nbsp;: <strong>{{ $manifest->received_at ? $manifest->received_at->format('H:i') : '___________________' }}</strong></div>
            </td>
        </tr>
    </table>
    <p>{!! $manifest->notes ? 'Catatan : ' . $manifest->notes : '' !!}</p>
    <br>
    <table border="1" class="labels-table">
        <thead>
            <tr>
                <th>{{ __('app.table_no') }}</th>
                <th>{{ __('receipt.number') }}</th>
                <th>{{ __('receipt.pack_type') }}</th>
                <th>{{ __('receipt.consignor') }}</th>
                <th>{{ __('receipt.consignor_address') }}</th>
                <th>{{ __('receipt.consignee') }}</th>
                <th>{{ __('receipt.consignee_address') }}</th>
                <th>{{ __('receipt.destination') }}</th>
                <th>Dus / Koli</th>
                <th>{{ __('receipt.weight') }}</th>
                <th>{{ __('service.service') }}</th>
                <th>COD</th>
                <th>{{ __('receipt.notes') }}</th>
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
                <td>{{ $receipt->number }}&nbsp;</td>
                <td>{{ $receipt->packType->name }}</td>
                <td>{{ $receipt->consignor['name'] }}</td>
                <td>
                    {{ $receipt->consignor['address'][1] }}
                    {{ $receipt->consignor['address'][2] }}
                    {{ $receipt->consignor['address'][3] }}
                </td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td>
                    {{ $receipt->consignee['address'][1] }}
                    {{ $receipt->consignee['address'][2] }}
                    {{ $receipt->consignee['address'][3] }}
                </td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->destination->name }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->items_count }} / {{ $receipt->pcs_count }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->weight }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->service() }}</td>
                <td class="text-right">
                    @if ($receipt->payment_type_id == '3')
                        {{ formatRp($receipt->bill_amount) }}
                        @php
                            $codTotal += $receipt->bill_amount;
                        @endphp
                    @endif
                </td>
                <td>{{ $receipt->notes }}</td>
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
                <th colspan="8">Jumlah</th>
                <th>{{ $itemCountTotal }} / {{ $pcsCountTotal }}</th>
                <th>{{ $itemWeightTotal }}</th>
                <th>&nbsp;</th>
                <th class="text-right">
                    @if ($codTotal)
                    {{ formatRp($codTotal) }}
                    @endif
                </th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <div><br>
        <table class="header-table" style="page-break-inside: avoid;">
            <tbody>
                <tr>
                    <th style="width:30%"><h2>{{ __('manifest.checker') }}</h2></th>
                    <th style="width:40%"><h2>{{ __('manifest.coordinator') }}</h2></th>
                    <th style="width:30%"><h2>{{ __('manifest.courier') }}</h2></th>
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