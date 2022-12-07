@inject('service', 'App\Entities\Services\Service')

@extends('layouts.plain')

@section('title', $province->id)

@section('content')
<table>
    <thead>
        <tr><th colspan="2"><h3>{{ __('rate.list_export') }}</h3></th></tr>
        <tr><td>{{ __('rate.origin') }}</td><td>{{ $originCity->id }} - {{ $originCity->name }}</td></tr>
        <tr><td>{{ __('rate.customer') }}</td><td>{{ $customer->name }}</td></tr>
        <tr><td>{{ __('address.province') }}</td><td>{{ $province->id }} - {{ $province->name }}</td></tr>
        <tr><td>Per</td><td>{{ dateId(date('Y-m-d')) }}</td></tr>
    </thead>
</table>
<table>
    <thead>
        <tr><th colspan="2"><h3>Keterangan</h3></th></tr>
        @foreach ($service::allRetailAndSalService() as $serviceId => $serviceName)
            <tr>
                <td>{{ $serviceName }}</td>
                <td>: {{ __('service.'.$serviceName) }}</td>
            </tr>
        @endforeach
    </thead>
</table>

<table>
    <tbody>
        <tr>
            <th>ID</th>
            <th>{{ __('address.city') }}/{{ __('address.district') }}</th>
            @foreach ($service::allRetailAndSalService() as $serviceId => $serviceName)
                <td>{{ $serviceName }}_kg</td>
                <td>{{ $serviceName }}_min</td>
                <td>{{ $serviceName }}_koli</td>
                <td>{{ $serviceName }}_etd</td>
            @endforeach
        </tr>
        @foreach ($province->cities as $city)
            <tr>
                <th>{{ $city->id }}</th>
                <th>{{ $city->name }}</th>
                @foreach ($service::allRetailAndSalService() as $serviceId => $serviceName)
                    @php
                        $matchedRate = $rates->filter(function ($rate) use ($serviceId, $city) {
                            return $serviceId == $rate->service_id && $city->id == $rate->dest_city_id;
                        })->first();
                    @endphp
                    <td>{{ optional($matchedRate)->rate_kg }}</td>
                    <td>{{ optional($matchedRate)->min_weight }}</td>
                    <td>{{ optional($matchedRate)->rate_pc }}</td>
                    <td>{{ optional($matchedRate)->etd }}</td>
                @endforeach
            </tr>
            @foreach ($city->districts as $district)
                <tr>
                    <td>{{ $district->id }}</td>
                    <td>{{ $district->name }}</td>
                    @foreach ($service::allRetailAndSalService() as $serviceId => $serviceName)
                        @php
                            $matchedRate = $rates->filter(function ($rate) use ($serviceId, $district) {
                                return $serviceId == $rate->service_id && $district->id == $rate->dest_district_id;
                            })->first();
                        @endphp
                        <td>{{ optional($matchedRate)->rate_kg }}</td>
                        <td>{{ optional($matchedRate)->rate_pc }}</td>
                        <td>{{ optional($matchedRate)->etd }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
        @endforeach
    </tbody>
</table>
@endsection
