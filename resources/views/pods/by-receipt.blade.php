@inject('podStatusCode', 'App\Entities\Receipts\Status')
@extends('layouts.app')
@section('title', trans('pod.entry_by_receipt'))

@section('content')

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm', 'id'=>'formChecker']) !!}
<div class="form-group">
    {!! Form::label('receipt_number', trans('receipt.number'), ['class'=>'control-label']) !!}
    {!! Form::text('receipt_number', Request::get('receipt_number'), ['class'=>'form-control','required']) !!}
    {!! Form::submit(trans('receipt.search'), ['class'=>'btn btn-info']) !!}
    {!! link_to_route('pods.by-receipt', trans('app.reset'), [], ['class' => 'btn btn-default']) !!}
</div>
{!! Form::close() !!}

@if (!is_null($receipt))
    <div class="row">
        <div class="col-md-4">
            @include('receipts.partials.receipt-data')
            @include('receipts.partials.receipt-consignor')
            @include('receipts.partials.receipt-consignee')
        </div>
        <div class="col-md-6">
            @include('pods.partials.pod-entry', [
                'returnLink' => link_to_route('pods.by-receipt', trans('app.cancel'), [], ['class' => 'btn btn-default pull-right']),
            ])
        </div>
    </div>
@else
    <div class="alert alert-{{ $infoClass }}">
        {!! $info !!}
    </div>
@endif
    <div id="cameraInit">Initlize Camera...Please Await</div>
    <div class="text-center">
        <video id="video" width="450" height="450"></video>
    </div>
@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#time,#deliver_at,#received_at').datetimepicker({
        format:'Y-m-d H:i'
    });
})();
</script>
@endsection
