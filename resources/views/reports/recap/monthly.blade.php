@php
$months = getMonths();
@endphp
@inject('regionQuery', 'App\Entities\Regions\RegionQuery')

@extends('layouts.app')

@section('title', 'Laporan Rekap Penjualan Bulanan : '.$months[$month].' '.$year)

@section('content')

{!! Form::open(['method' => 'get','class' => 'form-inline well well-sm text-center']) !!}

<div style="margin-bottom: 10px">
    {!! FormField::select('dest_city_id', $cities, ['label' => trans('receipt.destination'), 'placeholder' => trans('address.city')]) !!}
    {!! FormField::select('dest_district_id', $regionQuery->getDistrictsList(request('dest_city_id')), ['label' => false, 'placeholder' => 'Semua Kecamatan']) !!}
</div>
{!! FormField::select('month', $months, ['value' => $month, 'placeholder' => false, 'label' => 'Pilih Bulan']) !!}
{!! FormField::select('year', getYears(), ['value' => $year, 'placeholder' => false, 'label' => false]) !!}
{!! Form::submit('Lihat Laporan', ['class' => 'btn btn-info']) !!}
{!! link_to_route('reports.omzet-recap.monthly','Bulan ini',[],['class' => 'btn btn-default']) !!}
{!! Form::close() !!}

<div class="panel panel-default">
    <table class="table table-condensed table-bordered" style="font-size: 12px">
        <thead>
            <tr>
                <th rowspan="2" class="text-center text-middle">{{ trans('app.table_no') }}</th>
                <th rowspan="2" class="text-middle">Customer</th>
                <th colspan="4" class="text-center">Per Koli</th>
                <th colspan="3" class="text-center">Per Kg</th>
                <th rowspan="2" style="width: 150px" class="text-center text-middle">Total Penjualan</th>
            </tr>
            <tr>
                <th style="width: 50px" class="text-center text-muted">Resi</th>
                <th style="width: 50px" class="text-center text-muted">Koli</th>
                <th style="width: 50px" class="text-center text-muted">Dus</th>
                <th style="width: 150px" class="text-center">Penjualan</th>
                <th style="width: 50px" class="text-center text-muted">Resi</th>
                <th style="width: 50px" class="text-center text-muted">Kg</th>
                <th style="width: 150px" class="text-center">Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>UMUM</td>
                @php
                    $customerPcBasedRecap = $monthlyPcBasedOmzetRecap->where('customer_id', null)->first();
                    $pcBasedBillTotal = 0;
                @endphp
                <td class="text-right text-muted">{{ is_null($customerPcBasedRecap) ? '' : formatNo($customerPcBasedRecap->receipt_total) }}</td>
                <td class="text-right text-muted">{{ is_null($customerPcBasedRecap) ? '' : formatNo($customerPcBasedRecap->item_total) }}</td>
                <td class="text-right text-muted">{{ is_null($customerPcBasedRecap) ? '' : formatNo($customerPcBasedRecap->pcs_total) }}</td>
                <td class="text-right">{{ is_null($customerPcBasedRecap) ? '' : formatRp($pcBasedBillTotal = $customerPcBasedRecap->bill_total) }}</td>
                @php
                    $customerWeightBasedRecap = $monthlyWeightBasedOmzetRecap->where('customer_id', null)->first();
                    $weightBasedBillTotal = 0;
                @endphp
                <td class="text-right text-muted">{{ is_null($customerWeightBasedRecap) ? '' : formatNo($customerWeightBasedRecap->receipt_total) }}</td>
                <td class="text-right text-muted">{{ is_null($customerWeightBasedRecap) ? '' : formatDecimal($customerWeightBasedRecap->weight_total) }}</td>
                <td class="text-right">{{ is_null($customerWeightBasedRecap) ? '' : formatRp($weightBasedBillTotal = $customerWeightBasedRecap->bill_total) }}</td>
                <td class="text-right">{{ formatRp($pcBasedBillTotal + $weightBasedBillTotal) }}</td>
            </tr>
            @foreach($customers as $key  =>  $customer)
            <tr>
                <td class="text-center">{{ 2 + $key }}</td>
                <td>{{ link_to_route('customers.show', $customer->account_no.' - '.$customer->name, [$customer]) }}</td>
                @php
                    $customerPcBasedRecap = $monthlyPcBasedOmzetRecap->where('customer_id', $customer->id)->first();
                    $pcBasedBillTotal = 0;
                @endphp
                <td class="text-right text-muted">{{ is_null($customerPcBasedRecap) ? '' : formatNo($customerPcBasedRecap->receipt_total) }}</td>
                <td class="text-right text-muted">{{ is_null($customerPcBasedRecap) ? '' : formatNo($customerPcBasedRecap->item_total) }}</td>
                <td class="text-right text-muted">{{ is_null($customerPcBasedRecap) ? '' : formatNo($customerPcBasedRecap->pcs_total) }}</td>
                <td class="text-right">{{ is_null($customerPcBasedRecap) ? '' : formatRp($pcBasedBillTotal = $customerPcBasedRecap->bill_total) }}</td>
                @php
                    $customerWeightBasedRecap = $monthlyWeightBasedOmzetRecap->where('customer_id', $customer->id)->first();
                    $weightBasedBillTotal = 0;
                @endphp
                <td class="text-right text-muted">{{ is_null($customerWeightBasedRecap) ? '' : formatNo($customerWeightBasedRecap->receipt_total) }}</td>
                <td class="text-right text-muted">{{ is_null($customerWeightBasedRecap) ? '' : formatDecimal($customerWeightBasedRecap->weight_total) }}</td>
                <td class="text-right">{{ is_null($customerWeightBasedRecap) ? '' : formatRp($weightBasedBillTotal = $customerWeightBasedRecap->bill_total) }}</td>
                <td class="text-right">{{ formatRp($pcBasedBillTotal + $weightBasedBillTotal) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="2">{{ trans('app.total') }}</th>
                <th class="text-right">{{ formatNo($monthlyPcBasedOmzetRecap->sum('receipt_total')) }}</th>
                <th class="text-right">{{ formatNo($monthlyPcBasedOmzetRecap->sum('pcs_total')) }}</th>
                <th class="text-right">{{ formatNo($monthlyPcBasedOmzetRecap->sum('item_total')) }}</th>
                <th class="text-right">{{ formatRp($monthlyPcBasedOmzetRecap->sum('bill_total')) }}</th>
                <th class="text-right">{{ formatNo($monthlyWeightBasedOmzetRecap->sum('receipt_total')) }}</th>
                <th class="text-right">{{ formatDecimal($monthlyWeightBasedOmzetRecap->sum('weight_total')) }}</th>
                <th class="text-right">{{ formatRp($monthlyWeightBasedOmzetRecap->sum('bill_total')) }}</th>
                <th class="text-right">{{ formatRp($monthlyPcBasedOmzetRecap->sum('bill_total') + $monthlyWeightBasedOmzetRecap->sum('bill_total')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
<style>
.select2-container--default .select2-selection--single .select2-selection__rendered {
    text-align: left;
}
</style>
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#dest_city_id').select2();
})();
</script>
@endsection
