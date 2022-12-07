@inject('service', 'App\Entities\Services\Service')
@inject('city', 'App\Entities\Regions\City')
@inject('queryRegion', 'App\Entities\Regions\RegionQuery')
@inject('reference', 'App\Entities\References\Reference')
@extends('layouts.app')

@section('title', trans('rate.edit'))

@section('content')
@if (Request::get('action') == 'delete')
<div class="modal show" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>{{ trans('rate.delete') }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ trans('app.delete_confirm') }}</p>
            </div>
            <div class="modal-footer">
                {!! link_to_route('customers.rates.edit', trans('app.cancel'), [$customer->id, $rate->id], ['class' => 'btn btn-default']) !!}
                {!! FormField::delete(['route'=>['customers.rates.destroy', $customer->id, $rate->id], 'class'=>'pull-left'], trans('app.delete_confirm_button'), ['class'=>'btn btn-danger'], ['rate_id' => $rate->id]) !!}
            </div>
        </div>
    </div>
</div>
@endif
<h2 class="page-header">{{ trans('rate.edit') }} <small>{{ trans('customer.customer') . ' : ' .  $customer->name }}</small></h2>
{!! Form::model($rate, ['route'=>['customers.rates.update', $customer->id, $rate->id], 'method' => 'patch']) !!}
<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.origin') }}</h3></div>
            <div class="panel-body">
                {!! FormField::textDisplay(trans('rate.origin'), $rate->origin->name) !!}
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.destination') }}</h3></div>
            <div class="panel-body">
                {!! FormField::textDisplay(trans('rate.destination'), $rate->destination->name) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('service.service') }}</h3></div>
            <div class="panel-body">
                {!! FormField::textDisplay(trans('service.service'), $rate->service()) !!}
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
                {!! Form::submit(trans('customer.rate.update'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('customers.rates.index', trans('app.cancel_or_back'), [$customer->id], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Info Tambahan</h3></div>
            <div class="panel-body">
                {!! FormField::textarea('notes',['label'=> trans('app.notes')]) !!}
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
@if (Request::get('action') != 'delete')
{{ link_to_route('customers.rates.edit', trans('rate.delete'), [$customer->id, $rate->id, 'action' => 'delete'], ['class' => 'btn btn-danger']) }}
@endif
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
@endsection

@section('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
@endsection

@section('script')
<script>
    (function() {
        $('.city-select').select2();
    })();
</script>
@endsection