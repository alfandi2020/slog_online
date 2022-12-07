@inject('unitType', 'App\Entities\Networks\UnitType')

@extends('layouts.app')

@section('title', trans('delivery_unit.list'))

@section('content')

<div class="pull-right">
    {{ link_to_route('admin.networks.delivery-units', trans('delivery_unit.create'), [$network->id, 'action' => 'create'], ['class' => 'btn btn-success']) }}
</div>
<h2 class="page-header">
    {{ $network->name }} <small>{{ trans('delivery_unit.list') }}: {{ $network->deliveryUnits->count() }}</small>
</h2>

@include('admin.networks.partials.nav-tabs')

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default table-responsive">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('delivery_unit.name') }}</th>
                        <th>{{ trans('delivery_unit.plat_no') }}</th>
                        <th>{{ trans('delivery_unit.type') }}</th>
                        <th>{{ trans('delivery_unit.network') }}</th>
                        <th class="text-center">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($network->deliveryUnits as $key => $deliveryUnit)
                    <tr>
                        <td class="text-center">{{ 1 + $key }}</td>
                        <td>{{ $deliveryUnit->name }}</td>
                        <td>{{ $deliveryUnit->plat_no }}</td>
                        <td>{{ $deliveryUnit->type }}</td>
                        <td>{{ $deliveryUnit->network->name }}</td>
                        <td class="text-center">
                            {!! html_link_to_route('admin.networks.delivery-units', '', [$network->id, 'action' => 'edit', 'id' => $deliveryUnit->id], [
                                'id' => 'edit-delivery_unit-' . $deliveryUnit->id,
                                'icon' => 'edit',
                                'title' => trans('app.edit'),
                            ]) !!} |
                            {!! html_link_to_route('admin.networks.delivery-units', '', [$network->id, 'action' => 'delete', 'id' => $deliveryUnit->id], [
                                'id' => 'del-delivery_unit-' . $deliveryUnit->id,
                                'icon' => 'close',
                                'title' => trans('app.delete'),
                            ]) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        @if (Request::get('action') == 'create')
            {!! Form::open(['route' => ['admin.networks.delivery-unit-store', $network->id]]) !!}
            {{ Form::hidden('network_id', $network->id) }}
            {!! FormField::text('name') !!}
            {!! FormField::text('plat_no') !!}
            {!! FormField::radios('type_id', $unitType::dropdown()) !!}
            {!! FormField::textarea('description') !!}
            {!! Form::submit(trans('delivery_unit.create'), ['class' => 'btn btn-success']) !!}
            {{ link_to_route('admin.networks.delivery-units', trans('app.cancel'), [$network->id], ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
        @endif
        @if (Request::get('action') == 'edit' && $editableUnit)
            {!! Form::model($editableUnit, ['route' => ['admin.networks.delivery-unit-update', $network->id, $editableUnit->id], 'method' => 'patch']) !!}
            {{ Form::hidden('network_id', $network->id) }}
            {!! FormField::text('name') !!}
            {!! FormField::text('plat_no') !!}
            {!! FormField::radios('type_id', $unitType::dropdown()) !!}
            {!! FormField::textarea('description') !!}
            {!! FormField::radios('is_active', ['Inactive', 'Active']) !!}
            {!! Form::submit(trans('delivery_unit.update'), ['class' => 'btn btn-success']) !!}
            {{ link_to_route('admin.networks.delivery-units', trans('app.cancel'), [$network->id], ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
        @if (Request::get('action') == 'delete' && $editableUnit)
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('delivery_unit.delete') }}</h3></div>
                <div class="panel-body">
                    <label class="control-label">{{ trans('delivery_unit.name') }}</label>
                    <p>{{ $editableUnit->name }}</p>
                    <p>{{ $editableUnit->plat_no }}</p>
                    <p>{{ $editableUnit->description }}</p>
                </div>
                <hr style="margin:0">
                <div class="panel-body">{{ trans('app.delete_confirm') }}</div>
                <div class="panel-footer">
                    {!! FormField::delete(['route'=>['admin.networks.delivery-unit-destroy', $network->id, $editableUnit->id]], trans('app.delete_confirm_button'), ['class'=>'btn btn-danger'], ['delivery_unit_id'=>$editableUnit->id]) !!}
                    {{ link_to_route('admin.networks.delivery-units', trans('app.cancel'), [$network->id], ['class' => 'btn btn-default']) }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
