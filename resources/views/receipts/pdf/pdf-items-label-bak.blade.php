<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('receipt.print_items_title', ['number' => $receipt->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
</head>
<body>
    <?php $no = 1;?>
    @foreach ($receipt->items_detail as $item)
    <div style="display:block;width:382px">
        <table class="labels-table" border="1">
            <thead>
                <tr>
                    <td valign="middle" height="100px" width="40%" rowspan="2" class="text-center">
                        {!! Html::image(url('imgs/logo.png'), 'logo', ['class'=>'bam-print-logo','style' => 'width:50px']) !!}
                        <h4>{{ $receipt->network->name }}</h4>
                        <div style="font-size:70%">Telp. {{ $receipt->network->phone }}<br>{{ Option::get('website_address') }}
                        </div>
                    </td>
                    <th width="30%" class="text-center">{{ trans('receipt.origin') }}</th>
                    <th width="30%" class="text-center">{{ trans('receipt.destination') }}</th>
                </tr>
                <tr>
                    <th class="text-center">{{ $receipt->originName() }}</th>
                    <th class="text-center">{{ $receipt->destinationName() }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" class="text-center">
                        <?php $itemNo = $receipt->number.str_pad($no, 3, "0", STR_PAD_LEFT);?>
                        <p>{{ $receipt->number }}</p>
                        {!! generateBarcode($itemNo) !!}
                        <p>{{ $itemNo }}</p>
                    </td>
                </tr>
                <tr>
                    <td height="120px" colspan="3">
                        <strong>{{ trans('receipt.consignee') }} :</strong>
                        <p>{{ $receipt->consignee['name'] }}</p>
                        <p style="height:60px">
                            {{ $receipt->consignee['address'][1] }}
                            {{ $receipt->consignee['address'][2] }}
                            {{ $receipt->consignee['address'][3] }}
                            Kode Pos: {{ $receipt->consignee['postal_code'] }}<br>
                            Telp: {{ $receipt->consignee['phone'] }}
                        </p>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td height="60px" class="text-center">
                        <h3>{{ trans('receipt.pcs_count') }}</h3>
                        <h2>{{ $no }}/{{ count($receipt->items_detail) }}</h2>
                    </td>
                    <td class="text-center">
                        <h3>{{ trans('receipt.weight') }}</h3>
                        <h2>{{ formatDecimal($item['charged_weight']) }} Kg</h2>
                    </td>
                    <td class="text-center">
                        <h3>{{ trans('service.service') }}</h3>
                        <h2>{{ strtoupper($receipt->service()) }}</h2>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @if ($no % 1 == 0 && $no != count($receipt->items_detail))
        <div class="page-break"></div>
    @endif
    <?php $no++?>
    @endforeach

</body>
</html>