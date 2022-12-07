@inject('networksList', 'App\Entities\Networks\Network')
@extends('layouts.app')

@section('title', trans('manifest.returns.create'))

@section('content')
<h3 class="page-header">{{ trans('manifest.returns.create') }}</h3>
<div class="col-sm-4">
{{ Form::open(['route' => 'manifests.returns.store']) }}
{!! FormField::textDisplay(trans('manifest.orig_network'), auth()->user()->network->name) !!}
{!! FormField::select('dest_network_id', $networksList::pluck('name','id'), ['label' => trans('manifest.dest_network'), 'required' => true]) !!}
{!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
{{ Form::submit(trans('manifest.returns.create'), ['class' => 'btn btn-success']) }}
{{ link_to_route('manifests.returns.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
{{ Form::close() }}
</div>
@endsection