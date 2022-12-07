@extends('layouts.app')

@section('title', trans('rate.edit'))

@section('content')

{!! Form::model($rate, ['route'=>['rates.update', $rate->id], 'method' => 'patch']) !!}

<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.edit') }}</h3></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                {!! FormField::textDisplay(trans('rate.origin'), $rate->originName()) !!}
                {!! FormField::textDisplay(trans('rate.destination'), $rate->destinationName()) !!}
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-sm-6">{!! FormField::textDisplay(trans('service.service'), $rate->service()) !!}</div>
                    <div class="col-sm-6">{!! FormField::text('etd',['label'=> trans('rate.etd_note'), 'required' => true, 'addon' => ['after' => 'Hari']]) !!}</div>
                </div>
                <div class="row">
                    <div class="col-sm-6">{!! FormField::price('rate_kg',['label'=> trans('rate.rate_kg'), 'required' => true]) !!}</div>
                    <div class="col-sm-6">{!! FormField::price('rate_pc',['label'=> trans('rate.rate_pc')]) !!}</div>
                </div>
            </div>
            <div class="col-md-4">
                {!! FormField::textarea('notes',['label'=> trans('app.notes')]) !!}
            </div>
        </div>
    </div>

    <div class="panel-footer">
        {!! Form::submit(trans('rate.update'), ['class'=>'btn btn-primary']) !!}
        {!! link_to_route('rates.index', trans('app.back'), [], ['class'=>'btn btn-default']) !!}
        {!! link_to_route('rates.delete', trans('rate.delete'), [$rate->id], ['class'=>'btn btn-danger pull-right']) !!}
    </div>
</div>
{!! Form::close() !!}
@endsection

@section('ext_css')
    {!! Html::style(url('css/select2.min.css')) !!}
@endsection

@section('ext_js')
    {!! Html::script(url('js/select2.min.js')) !!}
@endsection

@section('script')
<script>
(function() {
    $('.city-select').select2();
})();

</script>
@endsection