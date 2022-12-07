@extends('layouts.plain')

@section('title', trans('rate.export_base_rates'))

@section('styles')
<style>
    body {
        font-family: 'Arial', sans-serif;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
<table>
    <thead>
        <tr><th colspan="2"><h3>{{ trans('rate.export_base_rates') }}</h3></th></tr>
        <tr><td>Per</td><td>{{ dateId(date('Y-m-d')) }}</td></tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('customer.customer') }}</th>
            <th>{{ trans('service.service') }}</th>
            <th>{{ trans('rate.orig_city') }}</th>
            <th>{{ trans('rate.orig_district') }}</th>
            <th>{{ trans('rate.dest_city') }}</th>
            <th>{{ trans('rate.dest_district') }}</th>
            <th>{{ trans('package_type.package_type') }}</th>
            <th>{{ trans('rate.rate_kg') }}</th>
            <th>{{ trans('rate.rate_pc') }}</th>
            <th>{{ trans('rate.min_weight') }}</th>
            <th>{{ trans('rate.etd') }}</th>
            <th>{{ trans('app.notes') }}</th>
            <th>{{ trans('app.last_update') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rates as $key => $rate)
        <tr>
            <td>{{ 1 + $key }}</td>
            <td>Umum</td>
            <td>{{ $rate->service() }}</td>
            <td>{{ $rate->cityOrigin->name }}</td>
            <td>{{ $rate->districtOrigin ? $rate->districtOrigin->name : '' }}</td>
            <td>{{ $rate->cityDestination->name }}</td>
            <td>{{ $rate->districtDestination ? $rate->districtDestination->name : '' }}</td>
            <td>{{ $rate->packTypeName() }}</td>
            <td>{{ $rate->rate_kg }}</td>
            <td>{{ $rate->rate_pc }}</td>
            <td>{{ $rate->min_weight }}</td>
            <td>{{ $rate->etd }}</td>
            <td>{{ $rate->notes }}</td>
            <td>{{ $rate->updated_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
