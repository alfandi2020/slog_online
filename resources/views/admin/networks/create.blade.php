@inject('networkType', 'App\Entities\Networks\Type')
@inject('regionQuery', 'App\Entities\Regions\RegionQuery')
@extends('layouts.app')

@section('title', trans('network.create'))

@section('content')
{!! Form::open(['route'=>'admin.networks.store']) !!}
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('network.show') }}</h3></div>
            <div class="panel-body">
                {!! FormField::text('name', ['label' => trans('app.name'), 'required' => true]) !!}
                {!! FormField::textarea('address', ['label' => trans('address.address')]) !!}
                <div class="row">
                    <div class="col-sm-6">{!! FormField::text('coordinate', ['label' => trans('address.coordinate')]) !!}</div>
                    <div class="col-sm-6">{!! FormField::text('postal_code', ['label' => trans('address.postal_code')]) !!}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('contact.contact') }}</h3></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">{!! FormField::text('phone', ['label' => trans('contact.phone')]) !!}</div>
                    <div class="col-sm-6">{!! FormField::email('email', ['label' => trans('contact.email')]) !!}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('network.origin') }}</h3></div>
            <div class="panel-body">
                {!! FormField::radios('type_id', $networkType::dropdown(), ['label' => trans('network.type'), 'required' => true]) !!}
                {!! FormField::select('province_id', $regionQuery->getProvincesList(), ['label' => trans('address.province'), 'placeholder' => trans('address.province'), 'required' => true]) !!}
                {!! FormField::select('origin_city_id', $regionQuery->getCitiesList(old('province_id')), ['label' => trans('network.origin_city'), 'placeholder' => trans('address.city'), 'required' => true]) !!}
                {!! FormField::select('origin_district_id', $regionQuery->getDistrictsList(old('origin_city_id')), ['label' => trans('network.origin_district'), 'placeholder' => trans('address.district')]) !!}
            </div>

            <div class="panel-footer">
                {!! Form::submit(trans('network.create'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('admin.networks.index', trans('app.cancel'), [], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection

@section('script')
<script>
(function() {
    $.ajaxSetup({
        headers: {"Authorization": "Bearer " + '{{ auth()->user()->api_token }}'}
    });
    $("#province_id").change(function(e) {
        var province_id = $("#province_id").val();
        if (province_id == '') return false;
        $.get(
            "{{ route('api.regions.cities') }}",
            {
                province_id: province_id
            },
            function(data) {
                var string = '<option value="">-- {{ trans('address.city') }} --</option>';
                $.each(data, function(index, value) {
                    string = string + `<option value="` + index + `">` + value + `</option>`;
                })
                $("#origin_city_id").html(string);
            }
        );
    });
    $("#origin_city_id").change(function(e) {
        var origin_city_id = $("#origin_city_id").val();
        if (origin_city_id == '') return false;
        $.get(
            "{{ route('api.regions.districts') }}",
            {
                city_id: origin_city_id
            },
            function(data) {
                var string = '<option value="">-- {{ trans('address.district') }} --</option>';
                $.each(data, function(index, value) {
                    string = string + `<option value="` + index + `">` + value + `</option>`;
                })
                $("#origin_district_id").html(string);
            }
        );
    });
})();
</script>
@endsection