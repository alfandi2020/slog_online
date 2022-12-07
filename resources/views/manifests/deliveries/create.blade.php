@inject('networksList', 'App\Entities\Networks\Network')
@extends('layouts.app')

@section('title', trans('manifest.deliveries.create'))

@section('content')
<h3 class="page-header">{{ trans('manifest.deliveries.create') }}</h3>
<div class="col-sm-4">
{{ Form::open(['route' => 'manifests.deliveries.store']) }}
{!! FormField::textDisplay(trans('manifest.orig_network'), auth()->user()->network->name) !!}
{!! FormField::select('dest_network_id', $networksList::pluck('name','id'), ['label' => trans('manifest.dest_network'), 'required' => true]) !!}
{!! FormField::select('delivery_unit_id', $couriers, ['label' => trans('manifest.deliveries.delivery_unit')]) !!}
<div class="row">
    <div class="col-sm-6">
        {!! FormField::text('weight', ['label' => trans('manifest.weight'), 'addon' => ['after' => 'kg'], 'class' => 'text-right']) !!}
    </div>
    <div class="col-sm-6">
        {!! FormField::text('pcs_count', ['label' => trans('manifest.pcs_count'), 'addon' => ['after' => 'Koli'], 'class' => 'text-right']) !!}
    </div>
</div>
{!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
{{ Form::submit(trans('manifest.deliveries.create'), ['class' => 'btn btn-success']) }}
{{ link_to_route('manifests.deliveries.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
{{ Form::close() }}
</div>
@endsection
