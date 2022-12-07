@inject('networkType', 'App\Entities\Networks\Type')
@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', trans('network.edit'))

@section('content')
{!! Form::model($network, ['route'=>['admin.networks.update', $network->id], 'method' => 'patch']) !!}
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('network.show') }}</h3></div>
            <div class="panel-body">
                {!! FormField::text('name', ['label' => trans('app.name'), 'required' => true]) !!}
                {!! FormField::textarea('address', ['label' => trans('address.address')]) !!}
                <div class="row">
                    <div class="col-sm-6">{!! FormField::text('coordinate', ['label' => trans('address.coordinate')]) !!}</div>
                    <div class="col-sm-6">{!! FormField::text('postal_code', ['label' => trans('address.postal_code')]) !!}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('contact.contact') }}</h3></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">{!! FormField::text('phone', ['label' => trans('contact.phone')]) !!}</div>
                    <div class="col-sm-6">{!! FormField::email('email', ['label' => trans('contact.email')]) !!}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('network.origin') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('type_id', $networkType::dropdown(), ['label' => trans('network.type'), 'required' => true]) !!}
                {!! FormField::select('province_id', $regionQuery->getProvincesList(), ['label' => trans('address.province'), 'placeholder' => trans('address.province'), 'required' => true, 'value' => $provinceId = $network->cityOrigin->province_id]) !!}
                {!! FormField::select('origin_city_id', $regionQuery->getCitiesList(old('province_id', $provinceId)), ['label' => trans('network.origin_city'), 'placeholder' => trans('address.city'), 'required' => true]) !!}
                {!! FormField::select('origin_district_id', $regionQuery->getDistrictsList(old('origin_city_id', $network->origin_city_id)), ['label' => trans('network.origin_district'), 'placeholder' => trans('address.district')]) !!}
            </div>
        </div>
    </div>
</div>
<div class="well well-sm">
    {!! Form::submit(trans('network.update'), ['class'=>'btn btn-primary']) !!}
    {!! link_to_route('admin.networks.show', trans('network.show'), [$network->id], ['class' => 'btn btn-info']) !!}
    {!! link_to_route('admin.networks.index', trans('network.back_to_index'), [], ['class' => 'btn btn-default']) !!}
    {!! link_to_route('admin.networks.delete', trans('network.delete'), [$network->id], ['class'=>'btn btn-danger pull-right']) !!}
</div>
{!! Form::close() !!}
@endsection