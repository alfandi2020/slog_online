@inject('networksList', 'App\Entities\Networks\Network')
@extends('layouts.app')
@section('title', trans('manifest.edit'))

@section('content')
<h3 class="page-header">
    <div class="pull-right">
        <span class="label label-info">{{ trans('app.date') }} : {{ $manifest->created_at->format('Y-m-d') }}</span>
        <span class="label label-success">{{ trans('manifest.orig_network') }} : {{ $manifest->originNetwork->name }}</span>
    </div>
    {{ trans('manifest.edit') }}
</h3>

{!! Form::model($manifest, ['route'=>['manifests.returns.update', $manifest->number], 'method' => 'patch']) !!}
<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ $manifest->number }}</h3></div>
    <div class="panel-body">
        <div class="row">

            <div class="col-md-4">
                {!! FormField::select('dest_network_id', $networksList::pluck('name','id'), ['label' => trans('manifest.dest_network'), 'required' => true]) !!}
                {!! FormField::textarea('notes') !!}
            </div>
        </div>
    </div>
    <div class="panel-footer">
        {!! Form::submit(trans('manifest.update'), ['class'=>'btn btn-primary']) !!}
        {!! link_to_route('manifests.returns.show', trans('app.cancel'), [$manifest->number], ['class'=>'btn btn-default']) !!}
    </div>
</div>
{!! Form::close() !!}

{!! FormField::delete(['route'=>['manifests.destroy',$manifest->number]], trans('manifest.delete'), ['class'=>'btn btn-danger'], ['manifest_id' => $manifest->id]) !!}
@endsection