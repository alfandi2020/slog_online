@inject('networksList', 'App\Entities\Networks\Network')
@extends('layouts.app')
@section('title', trans('manifest.edit').' - '.$manifest->number)

@section('content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data-delivery')
    </div>
    <div class="col-md-4">
        {!! Form::model($manifest, ['route'=>['manifests.deliveries.update', $manifest->number], 'method' => 'patch']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('manifest.edit') }}</h3></div>
            <div class="panel-body">
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
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('manifest.update'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('manifests.deliveries.show', trans('app.cancel'), [$manifest->number], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

{!! FormField::delete(['route'=>['manifests.destroy',$manifest->number]], trans('manifest.delete'), ['class'=>'btn btn-danger'], ['manifest_id' => $manifest->id]) !!}
@endsection
