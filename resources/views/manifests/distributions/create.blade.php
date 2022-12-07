@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', trans('manifest.distributions.create'))

@section('content')
<h3 class="page-header">{{ trans('manifest.distributions.create') }}</h3>
<div class="col-sm-4">
{{ Form::open(['route' => 'manifests.distributions.store']) }}
{!! FormField::textDisplay(trans('network.network'), auth()->user()->network->name) !!}
{!! FormField::select('dest_city_id', $regionQuery->getCitiesList(), ['label' => trans('manifest.distributions.dest_city'), 'required' => true]) !!}
{!! FormField::select('handler_id', $couriersList, ['label' => trans('manifest.courier'), 'required' => true]) !!}
{!! FormField::select('delivery_unit_id', $deliveryUnitsList, ['label' => trans('manifest.delivery_unit')]) !!}
{!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
{{ Form::submit(trans('manifest.distributions.create'), ['class' => 'btn btn-success']) }}
{{ link_to_route('manifests.distributions.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
{{ Form::close() }}
</div>
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
    $('#dest_city_id').select2();
})();
</script>
@endsection