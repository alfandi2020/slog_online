@extends('layouts.receipt-detail')

@section('subtitle', trans('receipt.costs_detail'))

@section('receipt-content')
@if (is_null($rate))
Tidak ada tarif dasar untuk resi ini.
@else
<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.base_rate') }}</h3></div>
            <table class="table table-condensed">
                <tbody>
                    <tr><th class="col-xs-5">{{ trans('rate.id') }}</th><td>{{ $rate->id }}</td></tr>
                    <tr><th>{{ trans('rate.type') }}</th><td>{{ $rate->customer_id ? 'Khusus ('.$rate->customer->name.')' : 'Umum' }}</td></tr>
                    <tr><th>{{ trans('rate.service') }}</th><td>{{ $rate->service() }}</td></tr>
                    <tr><th>{{ trans('rate.orig_city') }}</th><td>{{ $rate->cityOrigin ? $rate->cityOrigin->name : '' }}</td></tr>
                    <tr><th>{{ trans('rate.orig_district') }}</th><td>{{ $rate->districtOrigin ? $rate->districtOrigin->name : '' }}</td></tr>
                    <tr><th>{{ trans('rate.dest_city') }}</th><td>{{ $rate->cityDestination ? $rate->cityDestination->name : '' }}</td></tr>
                    <tr><th>{{ trans('rate.dest_district') }}</th><td>{{ $rate->districtDestination ? $rate->districtDestination->name : '' }}</td></tr>
                    <tr><th>{{ trans('rate.rate_kg') }}</th><td class="text-right">{{ formatRp($rate->rate_kg) }}</td></tr>
                    <tr><th>{{ trans('rate.rate_pc') }}</th><td class="text-right">{{ formatRp($rate->rate_pc) }}</td></tr>
                    <tr><th>{{ trans('rate.min_weight') }}</th><td class="text-right">{{ $rate->min_weight }} Kg</td></tr>
                    <tr><th>{{ trans('rate.notes') }}</th><td>{{ $rate->notes }}</td></tr>
                    <tr><th>{{ trans('rate.created_at') }}</th><td>{{ $rate->created_at }}</td></tr>
                    <tr><th>{{ trans('rate.updated_at') }}</th><td>{{ $rate->updated_at }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-success">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.charge_calculation') }} {{ $receipt->charged_on_label }}</h3></div>
            @if ($receipt->charged_on == 1)
                <table class="table">
                    <tr><th class="col-xs-4">{{ trans('receipt.weight') }}</th><td>=</td><td class="text-right">{{ $receipt->weight }} Kg</td></tr>
                    <tr><th>{{ trans('rate.rate_kg') }}</th><td>=</td><td class="text-right">{{ formatRp($receipt->base_rate) }}</td></tr>
                    @if ($rate->min_weight > 1 && $receipt->weight < $rate->min_weight)
                    <tr>
                        <th>{{ trans('rate.min_weight') }}</th>
                        <td>=</td>
                        <td class="text-right">{{ $rate->min_weight }} Kg</td>
                    </tr>
                    <tr>
                        <th>{{ trans('receipt.base_charge') }}</th>
                        <td>=</td>
                        <td class="text-right">
                            {{ $rate->min_weight }} Kg x {{ formatRp($receipt->base_rate) }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('receipt.base_charge') }}</th>
                        <td>=</td>
                        <td class="text-right">
                            <span class="lead text-primary strong">{{ formatRp($rate->min_weight * $receipt->base_rate) }}</span>
                        </td>
                    </tr>
                    @else
                    <tr><th>{{ trans('receipt.base_charge') }}</th><td>=</td><td class="text-right">{{ $receipt->weight }} Kg x {{ formatRp($receipt->base_rate) }}</td></tr>
                    <tr><th>{{ trans('receipt.base_charge') }}</th><td>=</td><td class="text-right"><span class="lead text-primary strong">{{ formatRp($receipt->weight * $receipt->base_rate) }}</span></td></tr>
                    @endif
                </table>
            @else
                <table class="table">
                    <tr><th class="col-xs-4">{{ trans('receipt.pcs_count') }}</th><td>=</td><td class="text-right">{{ $receipt->pcs_count }} Koli</td></tr>
                    <tr><th>{{ trans('rate.rate_pc') }}</th><td>=</td><td class="text-right">{{ formatRp($receipt->base_rate) }}</td></tr>
                    <tr><th>{{ trans('receipt.base_charge') }}</th><td>=</td><td class="text-right">{{ $receipt->pcs_count }} Koli x {{ formatRp($receipt->base_rate) }}</td></tr>
                    <tr><th>{{ trans('receipt.base_charge') }}</th><td>=</td><td class="text-right"><span class="lead text-primary strong">{{ formatRp($receipt->pcs_count * $receipt->base_rate) }}</span></td></tr>
                </table>
            @endif
        </div>
        @include('receipts.partials.receipt-package')
    </div>
    <div class="col-sm-4">
        @include('receipts.partials.receipt-costs-detail')
    </div>
</div>
@endif
@endsection
