<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('invoice.print_title', ['number' => $invoice->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
    <style>
        body { margin: 10px; }
        table.labels-table tbody td { padding: 2px; }
        table.header-table tbody td { padding: 4px; }
        table th { text-align: center; vertical-align: middle; }
        table th.lead { font-size: 120% }
        .operator-line {
            border-top: 2px solid #aaa;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width:25%">
                <p>Kepada:</p><br>
                <p class="strong">{{ $invoice->customer->name }}</p>
                <p>{{ $invoice->customer->present()->addresses }}</p>
                <p>UP. <b>{{ $invoice->customer->pic['name_acc'] ?? $invoice->customer->pic['name'] }}</b></p>
            </td>
            <th style="width:50%">
                {!! Html::image(url('imgs/logo.png'), 'logo', ['style'=>'width:120px']) !!}
                <h3>INVOICE</h3>
            </th>
            <td style="width:25%" class="text-right">
                <div style="margin-bottom:10px">{!! generateQrCode($invoice->number, 70) !!}</div>
                <p>{{ trans('invoice.number') }} : <b>{{ $invoice->number }}</b></p>
                <p>{{ trans('app.date') }} : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{ $invoice->date }}</b></p>
                <p>{{ trans('invoice.end_date') }} : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{ $invoice->end_date }}</b></p>
            </td>
        </tr>
    </table>
    <br>
    <table class="labels-table" style="font-size:8px;">
        <thead style="border-top:4px double #333;border-bottom:1px solid #333;">
            <tr>
                <th>{{ trans('app.table_no') }}</th>
                <th>{{ trans('app.date') }}</th>
                <th>{{ trans('receipt.number') }}</th>
                <th>{{ trans('receipt.customer_invoice_no') }}</th>
                <th>{{ trans('receipt.consignee') }}</th>
                <th>{{ trans('receipt.consignee_name') }}</th>
                <th>{{ trans('receipt.destination') }}</th>
                <th>{{ trans('receipt.weight') }}</th>
                <th>{{ trans('receipt.qty') }}</th>
                <th>{{ trans('service.service') }}</th>
                <th>{{ trans('receipt.packing_cost') }}</th>
                <th>{{ trans('receipt.base_rate') }}</th>
                <th>{{ trans('receipt.bill_amount') }}</th>
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
                <td style="vertical-align:top" class="text-left">{{ $receipt->customer_invoice_no }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td style="vertical-align:top">{{ isset($receipt->consignee['recipient']) ? $receipt->consignee['recipient'] : '-' }}</td>
                <td style="vertical-align:top" class="text-left">
                    @if ($receipt->destDistrict)
                    {{ $receipt->destDistrict->name }},
                    @endif
                    {{ $receipt->destCity->name }}
                </td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->weight }}</td>
                <td style="vertical-align:top" class="text-center">{{ $receipt->items_count }}</td>
                <td class="text-center">{{ $receipt->service() }}</td>
                <td class="text-right">{{ formatRp($receipt->costs_detail['packing_cost']) }}</td>
                <td class="text-right">{{ formatRp($receipt->base_rate) }}</td>
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
                <td style="width:350px">
                    <p>Catatan: {{ $invoice->notes }}</p>
                </td>
                <td class="text-right" style="width:250px">
                    <p>{{ trans('invoice.receipts_bill_amount') }} :</p>
                    <p>{{ trans('invoice.discount') }} :</p>
                    <p class="operator-line" style="border-color: #fff">{{ trans('app.subtotal') }} :</p>
                    <p>{{ trans('invoice.admin_fee') }} :</p>
                    @if ($invoice->customer->is_taxed)
                    <p class="operator-line" style="border-color: #fff">{{ trans('app.subtotal') }} :</p>
                    <p>{{ trans('invoice.base_tax') }} :</p>
                    <p>{{ trans('invoice.ppn') }} :</p>
                    @endif
                    <p class="strong operator-line" style="border-color: #fff">{{ trans('invoice.amount') }} :</p>
                </td>
                <td class="text-right" style="width:130px">
                    <p>{{ formatRp($billAmountTotal) }}</p>
                    <p>{{ formatRp($invoice->charge_details['discount']) }}</p>
                    <p class="operator-line">{{ formatRp($subtotal = $billAmountTotal - $invoice->charge_details['discount']) }}</p>
                    <p>{{ formatRp($invoice->charge_details['admin_fee']) }}</p>
                    @if ($invoice->customer->is_taxed)
                    <p class="operator-line">{{ formatRp($amount = $subtotal + $invoice->charge_details['admin_fee']) }}</p>
                    <p>{{ formatRp($amount) }}</p>
                    <!-- <p>{{ formatRp(0.1 * ($amount)) }}</p> -->
                    <p>{{ formatRp(0.011 * ($amount)) }}</p>
                    @endif
                    <p class="strong operator-line">{{ formatRp($total = $invoice->getAmount()) }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="header-table" style="page-break-inside: avoid;">
        <tbody>
            <tr>
                <td colspan="3">
                    <p style="font-size:14px">Terbilang: <strong>{{ ucwords(Terbilang::make($total)) }} Rupiah</strong></p><br>
                </td>
            </tr>
            <tr>
                <td style="width:30%" rowspan="2">
                    <p>Transfer melalui :</p>
                    @if ($invoice->network_id == 2)
                    <div style="border:1px solid #ccc;padding:5px;margin-bottom: 5px">
                        <strong>{{ \App\Entities\Transactions\PaymentMethod::where('id',4)->first()->name}}</strong>
                        <p>{!! nl2br(\App\Entities\Transactions\PaymentMethod::where('id',4)->first()->description) !!}</p>
                    </div>
                    @elseif ($invoice->network_id == 5)
                    <div style="border:1px solid #ccc;padding:5px;margin-bottom: 5px">
                        <strong>{{ \App\Entities\Transactions\PaymentMethod::where('id',5)->first()->name}}</strong>
                        <p>{!! nl2br(\App\Entities\Transactions\PaymentMethod::where('id',5)->first()->description) !!}</p>
                    </div>
                    @elseif ($invoice->network_id == 18)
                    <div style="border:1px solid #ccc;padding:5px;margin-bottom: 5px">
                        <strong>{{ \App\Entities\Transactions\PaymentMethod::where('id',6)->first()->name}}</strong>
                        <p>{!! nl2br(\App\Entities\Transactions\PaymentMethod::where('id',6)->first()->description) !!}</p>
                    </div>
                    @elseif ($invoice->network_id == 26)
                    <div style="border:1px solid #ccc;padding:5px;margin-bottom: 5px">
                        <strong>{{ \App\Entities\Transactions\PaymentMethod::where('id',7)->first()->name}}</strong>
                        <p>{!! nl2br(\App\Entities\Transactions\PaymentMethod::where('id',7)->first()->description) !!}</p>
                    </div>
                    @else
                    <div style="border:1px solid #ccc;padding:5px;margin-bottom: 5px">
                        <strong>{{ \App\Entities\Transactions\PaymentMethod::where('id',2)->first()->name}}</strong>
                        <p>{!! nl2br(\App\Entities\Transactions\PaymentMethod::where('id',2)->first()->description) !!}</p>
                        <strong>{{ \App\Entities\Transactions\PaymentMethod::where('id',3)->first()->name}}</strong>
                        <p>{!! nl2br(\App\Entities\Transactions\PaymentMethod::where('id',3)->first()->description) !!}</p>
                    </div>
                    @endif
                    {{-- @foreach ($bankAccounts as $bankAccount)
                        <div style="border:1px solid #ccc;padding:5px;margin-bottom: 5px">
                            <strong>{{ $bankAccount->name }}</strong>
                            <p>{!! nl2br($bankAccount->description) !!}</p>
                        </div>
                    @endforeach --}}
                </td>
                <td style="width:30%" rowspan="2">&nbsp;</td>
                <td class="text-center" style="width:40%">
                    <p>{{ $userNetwork->origin->name }}, {{ dateId($invoice->date) }}</p>
                    <p>Hormat Kami,</p>
                    <p class="strong">{{ config('app.company_name') }}</p>
                </td>
            </tr>
            <tr>
                <th class="text-center" style="height:130px;vertical-align: bottom;">
                    <p>({{ config('app.director_name') }})</p>
                    <p>Direktur</p>
                </th>
            </tr>
        </tbody>
    </table>
    <div class="page-break"></div>
    <br><br><br>
    @foreach(range(1,2) as $number)
    <table style="width:500px; margin: 0 auto;" class="header-table">
        <tbody>
            <tr>
                <td style="width:25%">
                    {!! Html::image(url('imgs/logo.png'), 'logo', ['style'=>'width:80px']) !!}
                </td>
                <th style="width:50%">
                    <h3>Tanda Terima</h3>
                </th>
                <th style="width:25%">&nbsp;</th>
            </tr>
        </tbody>
    </table>
    <br>
    <table style="width:500px; margin: 0 auto;" class="header-table">
        <tbody>
            <tr>
                <td>Telah terima dari</td>
                <td colspan="2">: {{ config('app.company_name') }}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;&nbsp;{{ $invoice->network->cityOrigin->name }}</td>
            </tr>
            <tr>
                <td>Ditujukan Kepada</td>
                <td colspan="2">: {{ $invoice->customer->name }}<td>
            </tr>
            <tr>
                <td>Berupa</td>
                <td colspan="2">: {{ trans('invoice.invoice') }} No. {{ $invoice->number }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right" height="50px">{{ $userNetwork->origin->name }}, {{ dateId($invoice->date) }},</th>
            </tr>
            <tr>
                <th style="width:33%">Yang menyerahkan,</th>
                <th style="width:33%">Yang membuat,</th>
                <th style="width:33%">Yang menerima,</th>
            </tr>
            <tr>
                <th style="height: 90px">&nbsp;</th>
                <th style="height: 90px">&nbsp;</th>
                <th style="height: 90px">&nbsp;</th>
            </tr>
            <tr>
                <th>(...........................................)</th>
                <th>({{ $invoice->creator->name }})</th>
                <th>(...........................................)</th>
            </tr>
        </tfoot>
    </table>
    @if ($loop->last == false)
    <hr style="border: 1px dashed #ccc; margin: 30px 0;">
    @endif
    @endforeach

    <div class="page-break"></div>

    <table style="width:770px; margin: 0 auto;border: 1px solid #777" class="header-table">
        <tbody>
            <tr>
                <td rowspan="5" class="text-center" style="border-right:1px solid #777;padding-right: 10px;width: 10%">
                    {!! Html::image(url('imgs/receipt-add-logo.jpg'), 'receipt-add-logo', ['style'=>'width:40px;']) !!}
                </td>
                <td colspan="2">&nbsp;</td>
                <th class="lead">Kuitansi</th>
            </tr>
            <tr>
                <td style="padding-left: 30px;width: 27%">Telah terima dari</td>
                <td colspan="2">: {{ $invoice->customer->name }}</td>
            </tr>
            <tr>
                <td style="padding-left: 30px">Uang Sebanyak</td>
                <td colspan="2">: {{ ucwords(Terbilang::make($total)) }} Rupiah</td>
            </tr>
            <tr>
                <td style="padding-left: 30px">Untuk Pembayaran</td>
                <td colspan="2">: {{ trans('invoice.invoice') }} No. {{ $invoice->number }}</td>
            </tr>
            <tr>
                <th height="50px" class="lead" style="padding-left: 10px">
                    {{ formatRp($total) }}
                </th>
                <td>&nbsp;</td>
                <th class="text-center">
                    {{ $userNetwork->origin->name }}, {{ dateId($invoice->date) }},
                </th>
            </tr>
            <tr>
                <td style="border-right:1px solid #777;">
                    {!! Html::image(url('imgs/logo.png'), 'logo', ['style'=>'width:80px;transform: rotate(270deg); transform-origin: 50%;margin-top: 14px;']) !!}
                </td>
                <td style="padding-left: 30px">&nbsp;</td>
                <td>&nbsp;</td>
                <th style="height:90px;vertical-align: bottom;">
                    <p>({{ config('app.director_name') }})</p>
                    <p>Direktur</p>
                </th>
            </tr>
        </tbody>
    </table>
</body>
</html>
