@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="pull-right">
    {{ Form::open(['method'=>'get','class'=>'form-inline']) }}
    {{ Form::select('province_id', $provinces, request('province_id'), ['class'=>'form-control input-sm','placeholder' => '-- ' . trans('address.province') . ' --']) }}
    {{ Form::submit(trans('region.show_cities'), ['class' => 'btn btn-info btn-sm']) }}
    {{ link_to_route('admin.regions.provinces', trans('app.back'), [], ['class' => 'btn btn-default btn-sm']) }}
    {{ Form::close() }}
</div>
<h2 class="page-header">{{ $pageTitle }}</h2>

@if (!is_null($cities))
<div class="panel panel-default table-responsive">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('region.cities_list') }}</h3></div>
    <table class="table table-condensed">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('app.code') }}</th>
            <th>{{ trans('address.city') }}</th>
            <th class="text-center">{{ trans('region.districts_count') }}</th>
        </thead>
        <tbody>
            @foreach($cities as $key => $city)
            <tr>
                <td class="text-center">{{ 1 + $key }}</td>
                <td class="text-center">{{ $city->id }}</td>
                <td>
                    {{ link_to_route('admin.regions.districts', $city->name, ['city_id' => $city->id], ['title' => trans('region.show_districts') . ' di ' . $city->name]) }}
                </td>
                <td class="text-center">
                    {{ link_to_route('admin.regions.districts', $city->districts_count, ['city_id' => $city->id], ['title' => trans('region.show_districts') . ' di ' . $city->name]) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th class="text-center">{{ trans('app.total') }}</th>
                <th>{{ $cities->count() }}</th>
                <th class="text-center">{{ $cities->sum('districts_count') }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endif
@endsection
