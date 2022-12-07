<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('receipt.print_title', ['number' => $receipt->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
</head>
<body>
    @foreach ($receiptDuplicates as $key => $value)
    <?php
    $showAble = false;
    if (in_array($key, [1,2,3]) && in_array($receipt->payment_type_id, [1,3]))
        $showAble = true;
    elseif (in_array($key, [3]) && $receipt->payment_type_id == 2)
        $showAble = true;
    elseif (in_array($key, [1,2,3,4,5]) && $receipt->payment_type_id == 3)
        $showAble = true;
    ?>
    <div>
        <table class="receipt-table">
            <tbody>
                <tr>
                    <td style="width:260px">@include('receipts.pdf.receipt-left-section')</td>
                    <td style="width:258px">@include('receipts.pdf.receipt-mid-section')</td>
                    <td style="width:250px">@include('receipts.pdf.receipt-right-section')</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if ($key != count($receiptDuplicates))
    <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>