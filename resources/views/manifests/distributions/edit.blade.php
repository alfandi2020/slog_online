@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')
@section('title', trans('manifest.edit').' - '.$manifest->number)

@section('content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data-distribution')
    </div>
    <div class="col-md-4">
        {!! Form::model($manifest, ['route'=>['manifests.distributions.update', $manifest->number], 'method' => 'patch']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ $manifest->number }}</h3></div>
            <div class="panel-body">
                @if ($manifest->receipts()->count() == 0 || is_null($manifest->dest_city_id))
                {!! FormField::select('dest_city_id', $regionQuery->getCitiesList(), ['label' => trans('manifest.distributions.dest_city'), 'required' => true]) !!}
                @else
                {!! FormField::textDisplay(trans('manifest.distributions.dest_city'), $manifest->destinationCity->name) !!}
                @endif
                {!! FormField::select('handler_id', $couriersList, ['label' => trans('manifest.courier'), 'required' => true]) !!}
                {!! FormField::select('delivery_unit_id', $deliveryUnitsList, ['label' => trans('manifest.delivery_unit')]) !!}
                <div class="row">
                    <div class="col-sm-6">
                        {!! FormField::text('deliver_at', ['label' => trans('manifest.deliver_at'), 'class' => 'time-select']) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! FormField::text('start_km', ['label' => trans('manifest.start_km')]) !!}
                    </div>
                </div>
                {{--
                <div class="row">
                    <div class="col-sm-6">
                        {!! FormField::text('received_at', ['label' => trans('manifest.received_at'), 'class' => 'time-select']) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! FormField::text('end_km', ['label' => trans('manifest.end_km')]) !!}
                    </div>
                </div>
                --}}
                {!! FormField::textarea('notes') !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('manifest.distributions.update'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('manifests.distributions.show', trans('app.cancel'), [$manifest->number], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

{!! FormField::delete(['route'=>['manifests.destroy',$manifest->number]], trans('manifest.delete'), ['class'=>'btn btn-danger'], ['manifest_id' => $manifest->id]) !!}
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
{!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
{!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#dest_city_id').select2();
    $('.time-select').datetimepicker({
        format:'Y-m-d H:i',
        closeOnTimeSelect: true
    });
})();
</script>
@endsection