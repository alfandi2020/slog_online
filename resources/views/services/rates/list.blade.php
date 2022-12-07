@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', trans('rate.list'))

@section('content')

<div class="well well-sm">
    {!! Form::open(['method'=>'get','class'=>'form-inline']) !!}
    {{ Form::select('orig_city_id', $regionQuery->getCitiesList(), request('orig_city_id'), [
        'placeholder' => '-- Pilih ' . trans('rate.origin') . ' --',
        'class' => 'city-select',
    ]) }}
    {{ Form::select('dest_city_id', $regionQuery->getCitiesList(), request('dest_city_id'), [
        'placeholder' => '-- Semua ' . trans('rate.destination') . ' --',
        'class' => 'city-select',
    ]) }}
    {!! Form::submit(trans('rate.search'), ['class' => 'btn btn-info']) !!}
    {!! link_to_route('rates.list', trans('app.reset'), [], ['class' => 'btn btn-default']) !!}
    <div class="pull-right">
        {{ link_to_route('rates.create', trans('rate.create'), [], ['class'=>'btn btn-success']) }}
        {{ link_to_route('rates.index', 'Tampilkan Wilayah', [], ['class' => 'btn btn-default']) }}
    </div>
    {!! Form::close() !!}
</div>
<div class="panel panel-default table-responsive">
    <table class="table table-condensed">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th>{{ trans('service.service') }}</th>
            <th>{{ trans('rate.origin') }}</th>
            <th>{{ trans('rate.destination') }}</th>
            <th class="text-right">{{ trans('rate.rate_kg') }}</th>
            <th class="text-right">{{ trans('rate.rate_pc') }}</th>
            <th>{{ trans('rate.etd') }}</th>
            <th>{{ trans('app.action') }}</th>
        </thead>
        <tbody>
            @forelse($rates as $key => $rate)
            <tr>
                <td>{{ $rates->firstItem() + $key }}</td>
                <td>{{ $rate->service() }}</td>
                <td>
                    {!! $rate->districtOrigin ? '<strong class="text-primary" style="display:inline-block">' . $rate->districtOrigin->name . '</strong>,' : '' !!}
                    {{ $rate->cityOrigin->name }}
                </td>
                <td>
                    {!! $rate->districtDestination ? '<strong class="text-primary" style="display:inline-block">' . $rate->districtDestination->name . '</strong>,' : '' !!}
                    {{ $rate->cityDestination->name }}
                </td>
                <td class="text-right">{{ formatRp($rate->rate_kg) }}</td>
                <td class="text-right">{{ formatRp($rate->rate_pc) }}</td>
                <td>{{ $rate->etd }} Hari</td>
                <td>
                    {!! link_to_route('rates.edit', trans('app.edit'), [$rate->id], ['title' => trans('rate.edit')]) !!}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">{{ trans('rate.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
    {!! str_replace('/?', '?', $rates->appends(Request::except('page'))->render()) !!}
@endsection

@section('ext_css')
{{ Html::style('css/plugins/select2.min.css') }}
@endsection

@push('ext_js')
{{ Html::script('js/plugins/select2.min.js') }}
@endpush

@section('script')
<script>
(function() {
    $(".city-select").select2();
})();
</script>
@endsection