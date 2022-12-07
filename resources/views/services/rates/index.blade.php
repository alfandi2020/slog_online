@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@inject('service', 'App\Entities\Services\Service')
@inject('rates', 'App\Entities\Services\Rate')
@extends('layouts.app')

@section('title', trans('rate.list'))

@section('content')

@include('services.partials.rate-index-options')

@include('services.partials.rate-destinations-nav-tabs')
@if ($destCity)
    {{-- @foreach($destCity->cities as $key => $city) --}}
    @include('services.rates.partials.city')
    {{-- @endforeach --}}
@else
    @foreach($provinceList as $key => $province)
    @include('services.rates.partials.province')
    @endforeach
@endif
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