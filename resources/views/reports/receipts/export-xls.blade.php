@inject('paymentType', 'App\Entities\Receipts\PaymentType')
@inject('statuses', 'App\Entities\Receipts\Status')
@inject('customers', 'App\Entities\Customers\Customer')
@inject('cities', 'App\Entities\Regions\City')
@inject('districts', 'App\Entities\Regions\District')
@inject('networks', 'App\Entities\Networks\Network')
@extends('layouts.plain')

@section('title', 'Export List Resi')

@section('content')

<table>
    <thead>
        <tr><th colspan="2"><h3>Export List Resi</h3></th></tr>
        <tr><td>Mulai</td><td>{{ $receiptQuery['start_date'] }}</td></tr>
        <tr><td>Hingga</td><td>{{ $receiptQuery['end_date'] }}</td></tr>
        <tr><td>{{ __('receipt.payment') }}</td><td>{{ $paymentType::getNameById($receiptQuery['payment_type_id']) }}</td></tr>
        <tr><td>{{ __('receipt.status') }}</td><td>{{ $statuses::getNameById($receiptQuery['status_code']) }}</td></tr>
        <tr><td>{{ __('receipt.network') }}</td><td>{{ optional($networks::find($receiptQuery['network_id']))->name }}</td></tr>
        <tr><td>{{ __('receipt.customer') }}</td><td>{{ optional($customers::find($receiptQuery['customer_id']))->name }}</td></tr>
        <tr>
            <td>{{ __('receipt.destination') }}</td>
            <td>{{ optional($cities::find($receiptQuery['dest_city_id']))->name }}</td>
        </tr>
        <tr>
            <td>{{ __('address.district') }}</td>
            <td>{{ optional($districts::find($receiptQuery['dest_district_id']))->name }}</td>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('service.service') }}</th>
            <th>{{ trans('receipt.number') }}</th>
            <th>{{ trans('app.date') }}</th>
            <th>{{ trans('app.time') }}</th>
            <th>{{ trans('receipt.network') }}</th>
            <th>{{ trans('receipt.consignee') }}</th>
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
            <th>{{ trans('app.status') }}</th>
            <th>Status Invoice</th>
            <th>Invoice Dibuat</th>
            <th>Invoice Dikirim</th>
            <th>Invoice Diterima</th>
            <th>Invoice Pelunasan</th>
            <th>Invoice Closing</th>
            <th>Nama Penerima (POD)</th>
            <th>Tanggal Terima</th>
            <th>Jam Terima</th>
            <th>{{ trans('receipt.customer_invoice_no') }}</th>
            <th>Tgl. Manifest Accounting</th>
            <th>Interval(days)</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $sumTotal = 0;
            $sumItemCount = 0;
            $sumItemWeight = 0;
        ?>
        @forelse($receipts as $key => $receipt)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $receipt->service() }}</td>
            <td>{{ $receipt->number }}&nbsp;</td>
            <td>{{ $receipt->pickup_time->format('Y-m-d') }}</td>
            <td>{{ $receipt->pickup_time->format('H:i') }}</td>
            <td>{{ $receipt->present()->creatorNetwork }}</td>
            <td>{{ $receipt->consignee['name'] }}</td>
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
            <td>{!! $receipt->present()->statusLabel !!}</td>
            <td>{!! $receipt->present()->paymentStatusLabel !!}</td>
            <td>{{ ! is_null($receipt->invoice) ? $receipt->invoice->created_at->format('Y-m-d') : '' }}</td>
            <td>{{ ! is_null($receipt->invoice) ? $receipt->invoice->sent_date : '' }}</td>
            <td>{{ ! is_null($receipt->invoice) ? $receipt->invoice->received_date : '' }}</td>
            <td>{{ ! is_null($receipt->invoice) ? $receipt->invoice->payment_date : '' }}</td>
            <td>{{ ! is_null($receipt->invoice) ? $receipt->invoice->verify_date : '' }}</td>
            <td>{{ isset($receipt->consignee['recipient']) ? $receipt->consignee['recipient'] : '' }}</td>
            <td>{{ ! is_null($receipt->proof) ? $receipt->proof->delivered_at->format('Y-m-d') : '' }}</td>
            <td>{{ ! is_null($receipt->proof) ? $receipt->proof->delivered_at->format('H:i') : '' }}</td>
            <td>{{ $receipt->customer_invoice_no }}</td>
            <td>{{ ! is_null($receipt->progress->where('start_status', 'ma')->first()) ? $receipt->progress->where('start_status', 'ma')->first()->created_at : '' }}</td>
            @if (!empty($receipt->proof) && !empty($receipt->progress->where('start_status', 'ma')->first()))
            <td>{{ (Carbon\Carbon::parse($receipt->proof->delivered_at))->diffInDays(Carbon\Carbon::parse($receipt->progress->where('start_status', 'ma')->first()->created_at)) }}</td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="21">{{ trans('receipt.no_receipts') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
