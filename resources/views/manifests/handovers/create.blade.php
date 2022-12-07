@extends('layouts.app')

@section('title', trans('manifest.handovers.create'))

@section('content')
<h3 class="page-header">{{ trans('manifest.handovers.create') }}</h3>
<div class="col-sm-4">
{{ Form::open(['route' => 'manifests.handovers.store']) }}
{!! FormField::textDisplay(trans('network.network'), auth()->user()->network->name) !!}
<div class="row">
    <div class="col-sm-6">
        {!! FormField::text('weight', ['label' => trans('manifest.weight'), 'addon' => ['after' => 'Kg'], 'class' => 'text-right']) !!}
    </div>
    <div class="col-sm-6">
        {!! FormField::text('pcs_count', ['label' => trans('manifest.pcs_count'), 'addon' => ['after' => 'Koli'], 'class' => 'text-right']) !!}
    </div>
</div>
{!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
{{ Form::submit(trans('manifest.handovers.create'), ['class' => 'btn btn-success']) }}
{{ link_to_route('manifests.handovers.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
{{ Form::close() }}
</div>
@endsection