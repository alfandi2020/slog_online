@extends('layouts.app')

@section('title', __('rate.list_export'))

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="panel panel-default">
            {{ Form::open(['route' => 'rates.exports.excel', 'method' => 'get']) }}
            <div class="panel-body">
                <legend>{{ __('rate.list_export') }}</legend>
                {!! FormField::select('customer_id', $customers, ['required' => true, 'class' => 'select2', 'placeholder' => false]) !!}
                {!! FormField::select('orig_city_id', $originCities, ['required' => true, 'label' => 'Kota/Kab. Asal', 'placeholder' => 'Pilih Kota/Kab. Asal', 'class' => 'select2', 'value' => auth()->user()->network->origin_city_id]) !!}
                {!! FormField::select('dest_prov_id', $destinationProvinces, ['required' => true, 'label' => 'Provinsi Tujuan', 'placeholder' => 'Pilih Provinsi Tujuan', 'class' => 'select2']) !!}
            </div>
            <div class="panel-footer">
                {{ Form::submit(__('rate.list_export'), ['class' => 'btn btn-default']) }}
                {{ link_to_route('rates.exports.index', __('app.reset'), [], ['class' => 'btn btn-default']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
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
    $('.select2').select2();
</script>
@endsection
