@extends('layouts.plain')

@section('title', trans('report.invoice'))

@section('styles')
<style>
.text-center {
    text-align: center;
}
.text-right {
    text-align: right;
}
</style>
@endsection

@section('content')

<table>
    <thead>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('invoice.number') }}</th>
            <th class="text-center">{{ trans('customer.account_no') }}</th>
            <th>{{ trans('customer.customer') }}</th>
            <th class="text-center">{{ trans('invoice.periode') }}</th>
            <th class="text-center">{{ trans('app.date') }}</th>
            <th class="text-center">{{ trans('invoice.end_date') }}</th>
            <th class="text-center">{{ trans('receipt.receipt') }}</th>
            <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
            <th class="text-center">{{ trans('app.status') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $sumCount = 0;
            $sumAmount = 0;
        ?>
        @foreach($invoices as $key => $invoice)
        <tr>
            <td class="text-center">{{ $key + 1 }}</td>
            <td>{{ $invoice->number }}</td>
            <td class="text-center">{{ $invoice->customer->account_no }}</td>
            <td>{{ $invoice->customer->name }}</td>
            <td class="text-center">{{ $invoice->periode }}</td>
            <td class="text-center">{{ $invoice->date }}</td>
            <td class="text-center">{{ $invoice->end_date }}</td>
            <td class="text-center">{{ $count = $invoice->receipts->count() }}</td>
            <td class="text-right">{{ $amount = $invoice->receipts->sum('bill_amount') }}</td>
            <td class="text-center">{!! $invoice->present()->statusLabel !!}</td>
        </tr>
        <?php
            $sumCount += $count;
            $sumAmount += $amount;
        ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" class="text-center">{{ trans('app.total') }}</th>
            <th class="text-center">{{ $sumCount }}</th>
            <th class="text-right">{{ formatRp($sumAmount) }}</th>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
</table>
@endsection
