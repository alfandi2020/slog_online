@extends('layouts.app')

@section('title')
{{ trans('manifest.show_title', ['type' => $manifest->type(), 'number' => $manifest->number]) }} |
@yield('subtitle', trans('manifest.show'))
@endsection

@section('content')

<div class="pull-right">
    @can('create-problem-manifest-of', $manifest)
    {{ link_to_route('manifests.problems.create', trans('manifest.problems.create'), ['manifest_number' => $manifest->number], ['class' => 'btn btn-warning']) }}
    @endcan
    @yield('show-links')
</div>

<h2 class="page-header">{{ $manifest->number }} <small>{{ trans('manifest.manifest') }} {{ $manifest->type() }}</small></h2>

@yield('manifest-content')

@endsection