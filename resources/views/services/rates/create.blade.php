@inject('service', 'App\Entities\Services\Service')
@inject('city', 'App\Entities\Regions\City')
@inject('queryRegion', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', trans('rate.create'))

@section('content')
<h2 class="page-header text-center">{{ trans('rate.create') }}</h2>
{!! Form::open(['route'=>'rates.list-store']) !!}
<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.origin') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('orig_city_id', $city::pluck('name','id'), ['label'=> trans('rate.orig_city'), 'required' => true, 'class' => 'city-select']) !!}
                {!! FormField::select('orig_district_id', $queryRegion->getDistrictsList(old('orig_city_id')), ['label'=> trans('rate.orig_district'), 'class' => 'city-select']) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.destination') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('dest_city_id', $city::pluck('name','id'), ['label'=> trans('rate.dest_city'), 'required' => true, 'class' => 'city-select']) !!}
                {!! FormField::select('dest_district_id', $queryRegion->getDistrictsList(old('dest_city_id')), ['label'=> trans('rate.dest_district'), 'class' => 'city-select']) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('service.service') }}</h3></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">{!! FormField::select('service_id', $service::dropdown(), ['label'=> trans('service.service'), 'required' => true]) !!}</div>
                </div>
                <div class="row">
                    <div class="col-sm-4">{!! FormField::text('etd',['label'=> trans('rate.etd'), 'required' => true, 'addon' => ['after' => 'Hari']]) !!}</div>
                    <div class="col-sm-4">{!! FormField::price('rate_kg',['label'=> trans('rate.rate_kg'), 'required' => true]) !!}</div>
                    <div class="col-sm-4">{!! FormField::price('rate_pc',['label'=> trans('rate.rate_pc')]) !!}</div>
                </div>
                {!! FormField::textarea('notes',['label'=> trans('app.notes')]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('rate.create'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('rates.index', trans('app.cancel'), [], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
    </div>

</div>
{!! Form::close() !!}
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
@endpush

@section('script')
<script>
    (function() {
        $('.city-select').select2();
    })();
</script>
@endsection