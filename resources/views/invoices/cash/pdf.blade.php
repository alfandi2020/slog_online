<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('invoice.print_title', ['number' => $invoice->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
    <style>
        body { margin: 20px; }
        table.labels-table tbody td { padding: 3px; }
        table.header-table tbody td { padding: 5px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width:250px">
                <h3>INVOICE TUNAI</h3>
                <p>{{ trans('app.date') }} : &nbsp;<b>{{ $invoice->date }}</b></p>
            </td>
            <th style="width:250px">
                {!! Html::image(url('imgs/logo.png'), 'logo', ['style'=>'width:120px']) !!}
            </th>
            <td style="width:250px" class="text-center">
                <div style="margin-bottom:10px">{!! generateQrCode($invoice->number, 70) !!}</div>
                <p>{{ trans('invoice.number') }} : <b>{{ $invoice->number }}</b></p>
            </td>
        </tr>
    </table>
    <br>
    <table class="labels-table" style="font-size:10px; width: auto;">
        <thead style="border-top:4px double #333;border-bottom:1px solid #333;">
            <tr>
                <th width="20px">{{ trans('app.table_no') }}</th>
                <th width="60px">{{ trans('app.date') }}</th>
                <th width="110px">{{ trans('receipt.number') }}</th>
                <th width="100px">{{ trans('receipt.consignee') }}</th>
                <th width="100px">{{ trans('receipt.destination') }}</th>
                <th width="50px">{{ trans('service.service') }}</th>
                <th width="70px">{{ trans('receipt.payment') }}</th>
                <th width="75px">{{ trans('receipt.base_rate') }}</th>
                <th width="70px">{{ trans('receipt.admin_fee') }}</th>
                <th width="95px">{{ trans('receipt.bill_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $itemCountTotal = 0;
                $itemWeightTotal = 0;
                $billAmountTotal = 0;
            ?>
            @foreach($invoice->receipts as $key => $receipt)
            <tr style="border-bottom:1px dotted #ccc">
                <td style="vertical-align:top" class="text-center">{{ $key + 1 }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->pickup_time->format('Y-m-d') }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->number }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td style="vertical-align:top" class="text-left">{{ $receipt->destination->name }}</td>
                <td style="vertical-align:top" class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->payment_type }}</td>
                <td class="text-right">{{ formatRp($receipt->base_rate) }}</td>
                <td class="text-right">{{ formatRp($receipt->costs_detail['admin_fee']) }}</td>
                <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
                <?php
                    $itemCountTotal += $receipt->items_count;
                    $itemWeightTotal += $receipt->weight;
                    $billAmountTotal += $receipt->bill_amount;
                ?>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="header-table" style="page-break-inside: avoid; font-size:12px;">
        <tbody>
            <tr>
                <td style="width:300px">
                    <p>Catatan: {{ $invoice->notes }}</p>
                </td>
                <td class="text-right" style="width:250px">
                    <strong style="font-size: 18px">Total :</strong>
                </td>
                <td class="text-right" style="width:130px">
                    <strong style="font-size: 18px">{{ formatRp($billAmountTotal) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="header-table" style="page-break-inside: avoid;">
        <tbody>
            <tr>
                <td colspan="3">
                    <p style="font-size:14px">
                        Terbilang: <strong><i>{{ ucwords(Terbilang::make($billAmountTotal)) }} Rupiah</i></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="text-center" style="width:30%">
                    <p>{{ $userNetwork->origin->name }}, {{ dateId($invoice->date) }}</p>
                </td>
                <td class="text-center" style="width:30%">Menerima,</td>
                <td class="text-center" style="width:40%">Mengetahui,</td>
            </tr>
            <tr>
                <th height="80px" class="text-center" style="vertical-align: bottom;">
                    {{ $invoice->creator->name }}
                </th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th class="text-center" style="vertical-align: bottom;">(Sales Counter)</th>
                <th class="text-center" style="vertical-align: bottom;">(Kasir)</th>
                <th class="text-center" style="vertical-align: bottom;">(Akunting)</th>
            </tr>
        </tbody>
    </table>
<div style="margin-top:10px;font-size:12px;">
<p>Transfer melalui :</p>
<p><strong>Bank BNI</strong></p>
<p>Rek. 0722689250</p>
<p>SINERGI LINTAS GLOBAL .PT</p>
</div>
</body>
</html>