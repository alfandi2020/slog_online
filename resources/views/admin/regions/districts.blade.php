@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="pull-right">{{ link_to_route('admin.regions.cities', trans('app.back'), ['province_id' => $city->province_id], ['class' => 'btn btn-default']) }}</div>
<h2 class="page-header">{{ $pageTitle }}</h2>

<div class="panel panel-default table-responsive">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('region.districts_list') }}</h3></div>
    <table class="table table-condensed">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('app.code') }}</th>
            <th>{{ trans('address.district') }}</th>
        </thead>
        <tbody>
            @foreach($districts as $key => $district)
            <tr>
                <td class="text-center">{{ 1 + $key }}</td>
                <td class="text-center">{{ $district->id }}</td>
                <td>{{ $district->name }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th class="text-center">{{ trans('app.total') }}</th>
                <th>{{ $districts->count() }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
