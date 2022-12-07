@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
<?php
$defaultCityId = request('orig_city_id', auth()->user()->network->origin_city_id);
?>
@extends('layouts.app')

@section('title', trans('nav_menu.get_costs'))

@section('content')
<div class="row">
    <div class="{{ $costs ? 'col-sm-3' : 'col-sm-4 col-sm-offset-4' }}">
    {{ Form::open(['route' => 'pages.get-costs','method' => 'get']) }}
        <legend class="text-center">{{ trans('nav_menu.get_costs') }}</legend>
        {!! FormField::select('orig_city_id', $regionQuery->getCitiesList(), [
            'id' => 'orig_city_id',
            'class' => 'city-select',
            'label' => false,
            'placeholder' => trans('rate.orig_city'),
            'required' => true,
            'value' => $defaultCityId
        ]) !!}
{{--         {!! FormField::select('orig_district_id', $regionQuery->getDistrictsList($defaultCityId), [
            'id' => 'orig_district_id',
            'class' => 'city-select',
            'label' => false,
            'placeholder' => trans('rate.orig_district'),
            'value' => request('orig_district_id')
        ]) !!} --}}
        {!! FormField::select('dest_city_id', $regionQuery->getCitiesList(), [
            'id' => 'dest_city_id',
            'class' => 'city-select',
            'label' => false,
            'placeholder' => trans('rate.dest_city'),
            'required' => true,
            'value' => request('dest_city_id')
        ]) !!}
{{--         {!! FormField::select('dest_district_id', $regionQuery->getDistrictsList(request('dest_city_id')), [
            'id' => 'dest_district_id',
            'class' => 'city-select',
            'label' => false,
            'placeholder' => trans('rate.dest_district'),
            'value' => request('dest_district_id')
        ]) !!} --}}
        <div class="row">
            <div class="col-xs-6">
                {!! FormField::text('charged_weight', [
                    'addon' => ['after' => 'KG'],
                    'label' => trans('rate.weight'),
                    'required' => true,
                    'value' => request('charged_weight', 1)
                ]) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">{{ link_to_route('pages.get-costs', trans('app.reset'), [], ['class' => 'btn btn-default btn-block']) }}</div>
            <div class="col-sm-6">{{ Form::submit(trans('service.get_costs'), ['class' => 'btn btn-info btn-block','name' => 'get-costs'])}}</div>
        </div>
    {{ Form::close() }}
    <br>
    </div>
    @if ($costs)
    <div class="col-sm-9">
        @include('services.partials.get-costs-results-div')
    </div>
    @endif
</div>
@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/select2.min.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/select2.min.js')) !!}
    {!! Html::script(url('js/plugins/jquery.autocomplete.min.js')) !!}
@endpush

@section('script')
<script>
    (function() {
        $.ajaxSetup({
            headers: {"Authorization": "Bearer " + '{{ auth()->user()->api_token }}'}
        });

        $.get("{{ route('api.regions.cities') }}",
            function(data) {
                var citiesArray = $.map(data, function(value, key) {
                    return {
                        value: value,
                        data: key
                    };
                });
                // initialize autocomplete with custom appendTo
                $('#autocomplete-custom-append').autocomplete({
                    lookup: citiesArray,
                    onSelect: function (suggestion) {
                        $(this).append('<input type="hidden" name="orig_city_id" value="' + suggestion.data + '">');

                        $.get("{{ route('api.regions.districts') }}", { city_id: suggestion.data },
                            function(data) {
                                var string = '<option value="">-- {{ trans('address.district') }} --</option>';
                                $.each(data, function(index, value) {
                                    string = string + `<option value="` + index + `">` + value + `</option>`;
                                })
                                $("#orig_district_id").html(string);
                            }
                        );
                    }
                });
            }
        );

        $('.city-select').select2();
        // $("#orig_prov_id,#dest_prov_id").change(function(e) {
        //     var province_id = $(this).val();
        //     var type = $(this).attr('id');
        //     if (province_id == '') return false;
        //     $.get("{{ route('api.regions.cities') }}", { province_id: province_id },
        //         function(data) {
        //             var string = '<option value="">-- {{ trans('address.city') }} --</option>';
        //             $.each(data, function(index, value) {
        //                 string = string + `<option value="` + index + `">` + value + `</option>`;
        //             })
        //             if (type == 'orig_prov_id')
        //                 $("#orig_city_id").html(string);
        //             else
        //                 $("#dest_city_id").html(string);
        //         }
        //     );
        // });
        $("#orig_city_id,#dest_city_id").change(function(e) {
            var city_id = $(this).val();
            var type = $(this).attr('id');
            if (city_id == '') return false;
            $.get("{{ route('api.regions.districts') }}", { city_id: city_id },
                function(data) {
                    var string = '<option value="">-- {{ trans('address.district') }} --</option>';
                    $.each(data, function(index, value) {
                        string = string + `<option value="` + index + `">` + value + `</option>`;
                    })
                    if (type == 'orig_city_id')
                        $("#orig_district_id").html(string);
                    else
                        $("#dest_district_id").html(string);
                }
            );
        });
    })();
</script>
@endsection