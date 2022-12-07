@extends('layouts.customer-detail')

@section('subtitle', trans('customer.rate.list'))

@section('show-links')
{!! link_to_route('customers.rates.create', trans('rate.create'), [$customer->id], ['class'=>'btn btn-success']) !!}
@endsection

@section('customer-content')
<div class="panel panel-default table-responsive">
    <div class="panel-body">
    <table class="table table-condensed table-hover">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('service.service') }}</th>
            <th>{{ trans('rate.origin') }}</th>
            <th>{{ trans('rate.destination') }}</th>
            <th class="text-right">{{ trans('rate.rate_kg') }}</th>
            <th class="text-right">{{ trans('rate.rate_pc') }}</th>
            <th class="text-right">{{ trans('rate.min_weight') }}</th>
            <th class="text-center">{{ trans('rate.etd') }}</th>
            <th class="text-center">{{ trans('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($rates as $key => $rate)
            <tr>
                <td>{{ 1 + $key }}</td>
                <td>{{ $rate->service() }}</td>
                <td>
                    {{ $rate->cityOrigin->name }}
                    {!! $rate->districtOrigin ? ', <span class="text-primary" style="display:inline-block">' . $rate->districtOrigin->name . '</span>' : '' !!}
                </td>
                <td>
                    {{ $rate->cityDestination->name }}
                    {!! $rate->districtDestination ? ', <span class="text-primary" style="display:inline-block">' . $rate->districtDestination->name . '</span>' : '' !!}
                </td>
                <td class="text-right">{{ formatRp($rate->rate_kg) }}</td>
                <td class="text-right">{{ formatRp($rate->rate_pc) }}</td>
                <td class="text-right">{{ $rate->min_weight }} Kg</td>
                <td class="text-right">{{ $rate->etd }} Hari</td>
                <td class="text-center">
                    {!! html_link_to_route('customers.rates.edit', '', [$customer->id, $rate->id], ['title' => trans('rate.edit'), 'icon' => 'edit']) !!}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">{{ trans('customer.rate.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
