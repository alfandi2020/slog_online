{{ Form::open(['route' => 'rates.store']) }}
{{ Form::hidden('orig_city_id', $origCityId) }}
{{ Form::hidden('service_id', $serviceId) }}
{{ Form::hidden('customer_id', $customerId) }}
<div class="panel panel-default rate-province-list">
    <div class="panel-heading text-center">
        <h3 class="panel-title">
            {{ $province->id . ' - ' . $province->name }}
        </h3>
    </div>
    <div class="panel-body">
        <span style="font-size: 14px; color: orange" class="pull-right">
            Harga diisi tanpa tanda titik (.) contoh: 13000 atau 7500.
        </span>
        List Kota & Kabupaten tujuan:
    </div>
    <table class="table table-condensed table-hover">
        <thead>
            <tr>
                <th class="col-md-1 text-center">Kode</th>
                <th class="col-md-3 text-left">Kota/Kabupaten</th>
                <th class="col-md-2 text-right">{{ trans('rate.rate_kg') }}</th>
                <th class="col-md-2 text-right">{{ trans('rate.rate_pc') }}</th>
                <th class="col-md-2 text-right">{{ trans('rate.min_weight') }} (Kg)</th>
                <th class="col-md-2 text-center">{{ trans('rate.etd') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($province->cities()->with('rates')->withCount('districts')->get() as $city)
            <?php
                $baseRate = null;

                if ($customerId) {
                    $baseRate = $city->rates->where('orig_city_id', $origCityId)
                        ->where('orig_district_id', 0)
                        ->where('dest_city_id', $city->id)
                        ->where('dest_district_id', 0)
                        ->where('service_id', $serviceId)
                        ->where('customer_id', 0)
                        ->first();
                }
                if (is_null($baseRate) && $customerId) continue;

                $rate = $city->rates->where('orig_city_id', $origCityId)
                    ->where('orig_district_id', 0)
                    ->where('dest_city_id', $city->id)
                    ->where('dest_district_id', 0)
                    ->where('service_id', $serviceId)
                    ->where('customer_id', $customerId)
                    ->first();
                $rateKg = !is_null($rate) ? $rate->rate_kg : '';
                $ratePc = !is_null($rate) ? $rate->rate_pc : '';
                $minWeight = !is_null($rate) ? $rate->min_weight : '';
                $rateEtd = !is_null($rate) ? $rate->etd : '';
            ?>
            <tr>
                <td class="text-center">{{ $city->id }}</td>
                <td class="text-left">
                    {{ $city->name }}
                    <span class="pull-right">
                    {{ link_to_route('rates.index', '(' . $city->districts_count . ' ' . trans('address.district') . ')', [
                        'dest_city_id' => $city->id] + Request::only([
                        'region', 'orig_city_id', 'service_id','customer_id'
                    ])) }}
                    </span>
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 3px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ formatRp($baseRate->rate_kg) }}</div>
                    @endif

                    Rp.
                    <input
                        title="Isi Tarif {{ $customerId ? 'Khusus' : 'Dasar' }}"
                        class="table-input text-right"
                        name="rate[{{ $city->id }}][kg]"
                        type="text"
                        value="{{ old('rate.' . $city->id . '.kg', $rateKg) }}">

                    {!! $errors->first('rate.' . $city->id . '.kg', '<div class="text-danger small">:message</div>') !!}
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 3px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ formatRp($baseRate->rate_pc) }}</div>
                    @endif

                    Rp.
                    <input
                        title="Isi Tarif {{ $customerId ? 'Khusus' : 'Dasar' }}"
                        class="table-input text-right"
                        name="rate[{{ $city->id }}][pc]"
                        type="text"
                        value="{{ old('rate.' . $city->id . '.pc', $ratePc) }}">

                    {!! $errors->first('rate.' . $city->id . '.pc', '<div class="text-danger small">:message</div>') !!}
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 3px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ $baseRate->min_weight }} Kg</div>
                    @endif

                    <input
                        title="Isi Tarif {{ $customerId ? 'Khusus' : 'Dasar' }}"
                        class="table-input text-right"
                        name="rate[{{ $city->id }}][min_weight]"
                        type="text"
                        value="{{ old('rate.'.$city->id.'.min_weight', $minWeight) }}">

                    {!! $errors->first('rate.'.$city->id.'.min_weight', '<div class="text-danger small">:message</div>') !!}
                </td>
                <td class="text-right">
                    @if ($baseRate)
                    <div style="margin-bottom: 3px; cursor: pointer;" title="Tarif Dasar" class="text-success strong">{{ $baseRate->etd }} &nbsp;Hari</div>
                    @endif

                    <input
                        title="Isi Tarif {{ $customerId ? 'Khusus' : 'Dasar' }}"
                        class="table-input"
                        name="rate[{{ $city->id }}][etd]"
                        type="text"
                        value="{{ old('rate.' . $city->id . '.etd', $rateEtd) }}"> Hari

                    {!! $errors->first('rate.' . $city->id . '.etd', '<div class="text-danger small">:message</div>') !!}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right">
                    {{ Form::submit(trans('rate.update'), [
                        'class' => 'btn btn-success',
                        'id' => 'update_'.$origCityId.'_'.$province->id.'_'.$serviceId.'_'.$customerId
                    ]) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
{{ Form::close() }}