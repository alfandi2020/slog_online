{{ Form::open(['route' => 'rates.store']) }}
{{ Form::hidden('orig_city_id', $origCityId) }}
{{ Form::hidden('service_id', $serviceId) }}
{{ Form::hidden('customer_id', $customerId) }}
<div class="panel panel-default rate-city-list">
    <div class="panel-heading text-center">
        <h3 class="panel-title">{{ $destCity->id . ' - ' . $destCity->name }}</h3>
    </div>
    <div class="panel-body">
        <?php
            $baseCityRate = null;
            if ($customerId) {
                $baseCityRate = $rates::where('orig_city_id', $origCityId)
                    ->where('dest_city_id', $destCity->id)
                    ->where('dest_district_id', 0)
                    ->where('service_id', $serviceId)
                    ->where('customer_id', 0)
                    ->first();
            }

            $cityRate = $rates::where('orig_city_id', $origCityId)
                ->where('dest_city_id', $destCity->id)
                ->where('dest_district_id', 0)
                ->where('service_id', $serviceId)
                ->where('customer_id', $customerId)
                ->first();
            $cityRateKg = !is_null($cityRate) ? $cityRate->rate_kg : '';
            $cityRatePc = !is_null($cityRate) ? $cityRate->rate_pc : '';
            $cityMinWeight = !is_null($cityRate) ? $cityRate->min_weight : '';
            $cityRateEtd = !is_null($cityRate) ? $cityRate->etd : '';
        ?>
        <div class="row">
            <div class="col-sm-6">Tarif ke {{ $destCity->id . ' - ' . $destCity->name }}</div>
            <div class="col-sm-2 text-right">
                @if ($baseCityRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ formatRp($baseCityRate->rate_kg) }}</div>
                @endif

                Rp.
                <input
                    class="table-input text-right"
                    name="rate[{{ $destCity->id }}][kg]"
                    type="text"
                    value="{{ old('rate.' . $destCity->id . '.kg', $cityRateKg) }}">

                {!! $errors->first('rate.' . $destCity->id . '.kg', '<div class="text-danger small">:message</div>') !!}
            </div>
            <div class="col-sm-2 text-right">
                @if ($baseCityRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ formatRp($baseCityRate->rate_pc) }}</div>
                @endif

                Rp.
                <input
                    class="table-input text-right"
                    name="rate[{{ $destCity->id }}][pc]"
                    type="text"
                    value="{{ old('rate.' . $destCity->id . '.pc', $cityRatePc) }}">

                {!! $errors->first('rate.' . $destCity->id . '.pc', '<div class="text-danger small">:message</div>') !!}
            </div>
            <div class="col-sm-2 text-right">
                @if ($baseCityRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ $baseCityRate->min_weight }} Kg</div>
                @endif

                <input
                    class="table-input text-right"
                    name="rate[{{ $destCity->id }}][min_weight]"
                    type="text"
                    value="{{ old('rate.'.$destCity->id.'.min_weight', $cityMinWeight) }}">

                {!! $errors->first('rate.'.$destCity->id.'.min_weight', '<div class="text-danger small">:message</div>') !!}
            </div>
            <div class="col-sm-2 text-right">
                @if ($baseCityRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ $baseCityRate->etd }} Hari</div>
                @endif

                <input
                    class="table-input"
                    name="rate[{{ $destCity->id }}][etd]"
                    type="text"
                    value="{{ old('rate.' . $destCity->id . '.etd', $cityRateEtd) }}"> Hari

                {!! $errors->first('rate.' . $destCity->id . '.etd', '<div class="text-danger small">:message</div>') !!}
            </div>
        </div>
    </div>
    <table class="table table-condensed table-hover">
        <thead>
            <tr>
                <th class="col-sm-1 text-center">Kode</th>
                <th class="col-sm-5 text-left">Kecamatan</th>
                <th class="col-sm-2 text-right">{{ trans('rate.rate_kg') }}</th>
                <th class="col-sm-2 text-right">{{ trans('rate.rate_pc') }}</th>
                <th class="col-md-2 text-right">{{ trans('rate.min_weight') }} (Kg)</th>
                <th class="col-sm-2 text-center">{{ trans('rate.etd') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($destCity->districts()->with('rates')->get() as $district)
            <?php
                $baseRate = null;

                if ($customerId) {
                    $baseRate = $district->rates->where('orig_city_id', $origCityId)
                        ->where('orig_district_id', 0)
                        ->where('dest_district_id', $district->id)
                        ->where('service_id', $serviceId)
                        ->where('customer_id', 0)
                        ->first();
                }
                if (is_null($baseRate) && $customerId) continue;

                $rate = $district->rates->where('orig_city_id', $origCityId)
                    ->where('dest_district_id', $district->id)
                    ->where('service_id', $serviceId)
                    ->where('customer_id', $customerId)
                    ->first();
                $rateKg = !is_null($rate) ? $rate->rate_kg : '';
                $ratePc = !is_null($rate) ? $rate->rate_pc : '';
                $minWeight = !is_null($rate) ? $rate->min_weight : '';
                $rateEtd = !is_null($rate) ? $rate->etd : '';
            ?>
            <tr>
                <td class="text-center">{{ $district->id }}</td>
                <td class="text-left">{{ $district->name }}</td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ formatRp($baseRate->rate_kg) }}</div>
                    @endif

                    Rp. <input class="table-input text-right" name="rate[{{ $district->id }}][kg]" type="text" value="{{ old('rate.' . $district->id . '.kg', $rateKg) }}">
                    {!! $errors->first('rate.' . $district->id . '.kg', '<div class="text-danger small">:message</div>') !!}
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ formatRp($baseRate->rate_pc) }}</div>
                    @endif

                    Rp. <input class="table-input text-right" name="rate[{{ $district->id }}][pc]" type="text" value="{{ old('rate.' . $district->id . '.pc', $ratePc) }}">
                    {!! $errors->first('rate.' . $district->id . '.pc', '<div class="text-danger small">:message</div>') !!}
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ $baseRate->min_weight }} Kg</div>
                    @endif

                    <input class="table-input text-right" name="rate[{{ $district->id }}][min_weight]" type="text" value="{{ old('rate.'.$district->id.'.min_weight', $minWeight) }}">
                    {!! $errors->first('rate.'.$district->id.'.min_weight', '<div class="text-danger small">:message</div>') !!}
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 4px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ $baseRate->etd }} Hari</div>
                    @endif

                    <input class="table-input" name="rate[{{ $district->id }}][etd]" type="text" value="{{ old('rate.' . $district->id . '.etd', $rateEtd) }}"> Hari
                    {!! $errors->first('rate.' . $district->id . '.etd', '<div class="text-danger small">:message</div>') !!}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">
                    {{ Form::submit(trans('rate.update'), [
                        'class' => 'btn btn-success',
                        'id' => 'update_'.$origCityId.'_'.$destCity->id.'_'.$serviceId.'_'.$customerId
                    ]) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
{{ Form::close() }}