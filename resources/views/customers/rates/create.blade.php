@inject('service', 'App\Entities\Services\Service')
@inject('city', 'App\Entities\Regions\City')
@inject('queryRegion', 'App\Entities\Regions\RegionQuery')
@inject('reference', 'App\Entities\References\Reference')
@extends('layouts.app')

@section('title', trans('customer.rate.create'))

@section('content')
<h2 class="page-header">{{ trans('customer.rate.create') }} <small>{{ trans('customer.customer') . ' : ' .  $customer->name }}</small></h2>
{!! Form::open(['route'=>['customers.rates.store', $customer->id]]) !!}
<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.origin') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('orig_city_id', $city::pluck('name','id'), ['label'=> trans('rate.orig_city'), 'required' => true, 'class' => 'city-select']) !!}
                {!! FormField::select(
                    'orig_district_id',
                    $queryRegion->getDistrictsList(old('orig_city_id')),
                    [
                        'label'=> trans('rate.orig_district'),
                        'class' => 'city-select',
                        'info' => [
                            'text' => 'Pilih Kecamatan asal jika tarif khusus <strong>antar Kecamatan</strong> atau dari <strong>Kecamatan ke Kabupaten</strong>.',
                            'class' => 'text-primary small',
                        ],
                    ]
                ) !!}
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.destination') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('dest_city_id', $city::pluck('name','id'), ['label'=> trans('rate.dest_city'), 'required' => true, 'class' => 'city-select']) !!}
                {!! FormField::select(
                    'dest_district_id',
                    $queryRegion->getDistrictsList(old('dest_city_id')),
                    [
                        'label'=> trans('rate.dest_district'),
                        'class' => 'city-select',
                        'info' => [
                            'text' => 'Pilih Kecamatan jika tarif khusus tujuan <strong>antar Kecamatan</strong> atau dari <strong>Kabupaten ke Kecamatan</strong>.',
                            'class' => 'text-primary small',
                        ],
                    ]
                ) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('service.service') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('service_id', $service::ratailAndSalDropdown(), ['label'=> trans('service.service'), 'required' => true]) !!}
                <div class="row">
                    <div class="col-sm-6">{!! FormField::price('rate_kg',['label'=> trans('rate.rate_kg'), 'required' => true]) !!}</div>
                    <div class="col-sm-6">{!! FormField::price('rate_pc',['label'=> trans('rate.rate_pc')]) !!}</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">{!! FormField::text('min_weight',['label'=> trans('rate.min_weight'), 'required' => true, 'addon' => ['after' => 'KG']]) !!}</div>
                    <div class="col-sm-6">
                        {!! FormField::text('discount',['label'=> trans('rate.discount'), 'addon' => ['after' => '%'], 'class' => 'text-right']) !!}
                        {!! FormField::price('add_cost',['label'=> trans('rate.add_cost')]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        {!! FormField::radios('pack_type_id', $reference::whereCat('pack_type')->pluck('name','id')->all(), [
                            'label' => trans('receipt.pack_type'),
                            'list_style' => 'unstyled',
                            'required' => true
                        ]) !!}
                    </div>
                    <div class="col-sm-6">{!! FormField::text('etd',['label'=> trans('rate.etd'), 'required' => true, 'addon' => ['after' => 'Hari']]) !!}</div>
                </div>
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('customer.rate.create'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('customers.rates.index', trans('app.cancel'), [$customer->id], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('service.service') }}</h3></div>
            <div class="panel-body">
                {!! FormField::textarea('notes',['label'=> trans('app.notes')]) !!}
            </div>
        </div>
    </div>

</div>
{!! Form::close() !!}
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
@endpush

@section('script')
<script>
    (function() {
        $('.city-select').select2();

        $.ajaxSetup({
            headers: {
                "Authorization": "Bearer " + '{{ auth()->user()->api_token }}',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#dest_city_id,#orig_city_id").change(function(e) {
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