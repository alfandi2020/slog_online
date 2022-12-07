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
                <tr style="border-style: hidden;">
                    <th colspan="2" class="text-center" style="padding-top: 10px;">
                        {!! Html::image(url('imgs/logo.png'), 'logo', ['class'=>'bam-print-logo','style' => 'width:50px']) !!}
                        <h4>{{ config('app.company_name') }}</h4>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="text-center">
                       <h2>{{ $receipt->number }}</h2> 
                        <div>
                            <?php $itemNo = $receipt->number.str_pad($no, 3, "0", STR_PAD_LEFT);?>
                            {!! generateBarcode($itemNo) !!}
                        </div>
                        <p>{{ $itemNo }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ trans('receipt.origin') }}</strong>
                        <h2 class="text-center">{{ $receipt->originName() }}</h2>
                    </td>
                    <td>
                        <strong>{{ trans('receipt.destination') }}</strong>
                        <h2 class="text-center">{{ $receipt->destinationName() }}</h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ trans('receipt.pcs_count') }}</strong>
                        <h2 class="text-center">{{ $no }}/{{ count($receipt->items_detail) }}</h2>
                    </td>
                    <td>
                        <strong>{{ trans('receipt.weight') }}</strong>
                        <h2 class="text-center">{{ formatDecimal($item['charged_weight']) }} Kg</h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>{{ trans('receipt.consignee') }}</strong>
                        <p>{{ $receipt->consignee['name'] }}</p>
                        <p>
                            {{ $receipt->consignee['address'][1] }}
                            {{ $receipt->consignee['address'][2] }}
                            {{ $receipt->consignee['address'][3] }}
                            Kode Pos: {{ $receipt->consignee['postal_code'] }}<br>
                            Telp: {{ $receipt->consignee['phone'] }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @if ($no % 1 == 0 && $no != count($receipt->items_detail))
        <div class="page-break"></div>
    @endif
    <?php $no++?>
    @endforeach

</body>
</html>