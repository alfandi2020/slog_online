@extends('layouts.app')
@section('title', trans('manifest.edit').' - '.$manifest->number)

@section('content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data-accounting')
    </div>
    <div class="col-md-4">

        {!! Form::model($manifest, ['route'=>['manifests.handovers.update', $manifest->number], 'method' => 'patch']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('manifest.edit') }}</h3></div>
            <div class="panel-body">
                {!! FormField::textarea('notes') !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('manifest.update'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('manifests.handovers.show', trans('app.cancel'), [$manifest->number], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

{!! FormField::delete(['route'=>['manifests.destroy',$manifest->number]], trans('manifest.delete'), ['class'=>'btn btn-danger'], ['manifest_id' => $manifest->id]) !!}
@endsection