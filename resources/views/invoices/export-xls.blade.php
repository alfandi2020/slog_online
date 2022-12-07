@extends('layouts.plain')

@section('title', 'Inv-' . $invoice->number)

@section('content')

<table>
    <thead>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('service.service') }}</th>
            <th>{{ trans('receipt.number') }}</th>
            <th>{{ trans('app.date') }}</th>
            <th>{{ trans('app.time') }}</th>
            <th>{{ trans('receipt.consignee') }}</th>
            <th>Nama Penerima (POD)</th>
            <th>{{ trans('receipt.consignor') }}</th>
            <th>{{ trans('receipt.orig_city') }}</th>
            <th>{{ trans('receipt.orig_district') }}</th>
            <th>{{ trans('receipt.dest_city') }}</th>
            <th>{{ trans('receipt.dest_district') }}</th>
            <th>{{ trans('receipt.pcs_count') }}</th>
            <th>{{ trans('receipt.items_count') }}</th>
            <th>{{ trans('receipt.weight') }}</th>
            <th>{{ trans('receipt.base_rate') }}</th>
            <th>{{ trans('receipt.base_charge') }}</th>
            <th>{{ trans('receipt.discount') }}</th>
            <th>{{ trans('receipt.subtotal') }}</th>
            <th>{{ trans('receipt.packing_cost') }}</th>
            <th>{{ trans('receipt.insurance_cost') }}</th>
            <th>{{ trans('receipt.admin_fee') }}</th>
            <th>{{ trans('receipt.bill_amount') }}</th>
            <th>{{ trans('receipt.pack_type') }}</th>
            <th>{{ trans('receipt.pack_content') }}</th>
            <th>{{ trans('receipt.package_value') }}</th>
            <th>{{ trans('receipt.notes') }}</th>
            <th>{{ trans('receipt.reference_no') }}</th>
            <th>{{ trans('receipt.payment_type') }}</th>
            <th>{{ trans('receipt.charged_on') }}</th>
            <th>{{ trans('receipt.delivery_courier') }}</th>
            <th>{{ trans('receipt.customer_invoice_no') }}</th>
            <th>{{ trans('app.status') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $sumTotal = 0;
            $sumItemCount = 0;
            $sumItemWeight = 0;
        ?>
        @forelse($invoice->receipts as $key => $receipt)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $receipt->service() }}</td>
            <td>{{ $receipt->number }}&nbsp;</td>
            <td>{{ $receipt->pickup_time->format('Y-m-d') }}</td>
            <td>{{ $receipt->pickup_time->format('H:i') }}</td>
            <td>{{ $receipt->consignee['name'] }}</td>
            <td>{{ $receipt->consignee['recipient'] }}</td>
            <td>{{ $receipt->consignor['name'] }}</td>
            <td>{{ $receipt->origCityName() }}</td>
            <td>{{ $receipt->origDistrictName() }}</td>
            <td>{{ $receipt->destCityName() }}</td>
            <td>{{ $receipt->destDistrictName() }}</td>
            <td>{{ $receipt->pcs_count }}</td>
            <td>{{ $receipt->items_count }}</td>
            <td>{{ $receipt->weight }}</td>
            <td>{{ $receipt->base_rate }}</td>
            <td>{{ $receipt->costs_detail['base_charge'] }}</td>
            <td>{{ $receipt->costs_detail['discount'] }}</td>
            <td>{{ $receipt->costs_detail['subtotal'] }}</td>
            <td>{{ $receipt->costs_detail['packing_cost'] }}</td>
            <td>{{ $receipt->costs_detail['insurance_cost'] }}</td>
            <td>{{ $receipt->costs_detail['admin_fee'] }}</td>
            <td>{{ $receipt->bill_amount }}</td>
            <td>{{ $receipt->packType->name }}</td>
            <td>{{ $receipt->pack_content }}</td>
            <td>{{ $receipt->package_value }}</td>
            <td>{{ $receipt->notes }}</td>
            <td>{{ $receipt->reference_no }}</td>
            <td>{{ $receipt->present()->paymentType }}</td>
            <td>{{ $receipt->present()->chargedOn }}</td>
            <td>{{ $receipt->deliveryCourierName() }}</td>
            <td>{{ $receipt->customer_invoice_no }}</td>
            <td>{!! $receipt->present()->statusLabel !!}</td>
        </tr>
        @empty
        <tr>
            <td colspan="22">{{ trans('receipt.no_receipts') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection