@extends('layouts.app')

@section('title', trans('nav_menu.home'))

@section('content')
<div class="row">
    @can('create_receipt')
    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <legend>Entry Resi Baru</legend>
                {!! FormField::formButton(['route' => 'receipts.add-receipt'], trans('receipt.create'), [
                    'class' => 'btn btn-default'
                ], ['service_id' => 14, 'orig_city_id' => auth()->user()->network->origin_city_id]) !!}
                {!! FormField::formButton(['route' => 'receipts.add-project-receipt'], trans('receipt.41.create'), [
                    'class' => 'btn btn-default','title' => trans('receipt.41.create_link_title')
                ], ['service_id' => 41]) !!}
            </div>
        </div>
    </div>
    @endcan
    @can('pod_by_receipt')
    <div class="text-center" style="margin-bottom: 50px;">
        <a href="{{ route('home') }}">
            {{ Html::image(url('imgs/logo.png'), trans('nav_menu.home') . ' | ' . config('app.name'), [
                'title' => trans('nav_menu.home') . ' | ' . config('app.name'), 'style' => 'width: 100px;',
            ]) }}
        </a>
    </div>
    <div class="container text-center">
        <ul class="nav navbar">
            <li style="margin-bottom: 10px;">{!! html_link_to_route('pods.by-receipt', 'Update Delivery', [], ['class' => 'btn btn-primary', 'icon' => 'check-square-o']) !!}</li>
            <li>{!! html_link_to_route('logout', trans('auth.logout'), [], ['class' => 'btn btn-primary', 'icon' => 'sign-out', 'id' => 'logout-button']) !!}</li>
        </ul>
    </div>
    @endcan
    @if (auth()->user()->role_id != 7)
    <div class="col-lg-4">@include('pages.partials.manifest-summary')</div>
    <div class="col-lg-4">@include('pages.partials.latest-receipts')</div>
    @endif
</div>
@endsection
