<legend>
    <div class="pull-right text-muted small">
        {{ $costs->getOrigin() }} ke {{ $costs->getDestination() }}
    </div>
    Hasil Pengecekan
</legend>
<br>
<div class="row">
    <div class="col-sm-5 strong">{{ trans('rate.destination') }}</div>
    <div class="col-sm-2 strong">{{ trans('service.service') }}</div>
    <div class="col-sm-2 strong text-right">{{ trans('rate.cost') }}</div>
    <div class="col-sm-1 strong text-center">Berat</div>
    <div class="col-sm-2 strong text-center">{{ trans('app.action') }}</div>
</div>

<hr style="margin-top: 10px; margin-bottom: 10px; border-top: 3px solid #9b59bb;">

@if ($costs->rates->isEmpty())
    <p>{{ trans('rate.not_found') }}</p>
@else
    @foreach($costs->rates as $rate)
    <div class="row" style="background-color: #F8F8F8; padding: 6px 0; margin-bottom: 4px">
        <div class="col-sm-5">
            {!! $rate->districtOrigin ? '<strong class="text-primary" style="display:inline-block">' . $rate->districtOrigin->name . '</strong>,' : '' !!}
            {{ $rate->cityOrigin->name }} <i class="fa fa-long-arrow-right"></i>
            {!! $rate->districtDestination ? '<strong class="text-primary" style="display:inline-block">' . $rate->districtDestination->name . '</strong>,' : '' !!}
            {{ $rate->cityDestination->name }}
        </div>
        <div class="col-sm-2">{{ $rate->service() }} ({{ $rate->etd }} Hari)</div>
        <div class="col-sm-2 text-right">{{ formatRp($rate->cost) }}</div>
        <div class="col-sm-1 text-center">{{ $costs->weight }} Kg</div>
        <div class="col-sm-2 text-center">
            @if (auth()->user()->can('create-receipt', $rate))
            {{ Form::open(['route' => 'receipts.add-receipt']) }}
            {{ Form::hidden('rate_id', $rate->id) }}
            {{ Form::hidden('charged_weight', $costs->weight) }}
            {{ Form::hidden('service_id', $rate->service_id) }}
            {{ Form::hidden('orig_city_id', $rate->orig_city_id) }}
            {{ Form::hidden('orig_district_id', $rate->orig_district_id) }}
            {{ Form::hidden('dest_city_id', $rate->dest_city_id) }}
            {{ Form::hidden('dest_district_id', $rate->dest_district_id) }}
            {{ Form::submit(trans('receipt.create'), [
                'class' => 'btn btn-info btn-xs',
                'title' => trans('receipt.create_link_title', ['service' => $rate->service()]),
                'id' => 'create-receipt-' . $rate->id,
            ]) }}
            {{ Form::close() }}
            @endif
        </div>
    </div>
    @endforeach
@endif