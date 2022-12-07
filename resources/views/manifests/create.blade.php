@extends('layouts.app')

@section('title', trans('manifest.create'))

@section('content')
<h3 class="page-header">Pilih Manifest yang akan dibuat</h3>
{{ link_to_route('manifests.handovers.create', trans('manifest.handovers.create')) }}
@endsection
