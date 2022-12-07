@extends('layouts.app')

@section('title', trans('address.province'))

@section('content')
<h2 class="page-header">{{ trans('nav_menu.regions') }}</h2>

<div class="panel panel-default table-responsive">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('address.province') }}</h3></div>
    <table class="table table-condensed">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('app.code') }}</th>
            <th>{{ trans('address.province') }}</th>
            <th class="text-center">{{ trans('region.cities_count') }}</th>
            <th class="text-center">{{ trans('region.districts_count') }}</th>
        </thead>
        <tbody>
            @foreach($provinces as $key => $province)
            <tr>
                <td class="text-center">{{ 1 + $key }}</td>
                <td class="text-center">{{ $province->id }}</td>
                <td>{{ link_to_route('admin.regions.cities', $province->name, ['province_id' => $province->id], ['title' => trans('region.show_cities') . ' di ' . $province->name]) }}</td>
                <td class="text-center">
                    {{ link_to_route('admin.regions.cities', $province->cities_count, ['province_id' => $province->id], ['title' => trans('region.show_cities') . ' di ' . $province->name]) }}
                </td>
                <td class="text-center">{{ $province->districts_count }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th class="text-center">{{ trans('app.total') }}</th>
                <th>{{ $provinces->count() }}</th>
                <th class="text-center">{{ $provinces->sum('cities_count') }}</th>
                <th class="text-center">{{ $provinces->sum('districts_count') }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
