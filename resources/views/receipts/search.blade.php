@extends('layouts.app')

@section('title', trans('receipt.search'))

@section('content')
{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm', 'id'=>'formChecker']) !!}
<div class="form-group">
    {!! Form::label('query', trans('receipt.search_page_label'), ['class'=>'control-label']) !!}
    {!! Form::text('query', Request::get('query'), ['class'=>'form-control','required','style' => 'width:200px']) !!}
    {!! Form::submit(trans('receipt.search'), ['class'=>'btn btn-info']) !!}
    {!! link_to_route('receipts.search', trans('app.reset'), [], ['class' => 'btn btn-default']) !!}
</div>
{!! Form::close() !!}

@if (empty($receipts))
<div class="alert alert-info">
    {{ trans('receipt.search_alert_info') }}
</div>
@else
@include('receipts.partials.receipt-index')

{!! str_replace('/?', '?', $receipts->appends(Request::except('page'))->render()) !!}
@endif
<div id="cameraInit">Initlize Camera...Please Await</div>
<video id="video" width="300" height="300"></video>

@endsection