@php
$date = Request::get('date', date('Y-m-d'));
@endphp
@extends('layouts.app')

@section('title', trans('receipt.index_html_title', ['date' => $date]))

@section('content')

<div class="pull-right">
    {!! Form::open(['method'=>'get','class'=>'form-inline']) !!}
    <div class="form-group">
        {!! Form::label('date', 'Tanggal', ['class'=>'control-label']) !!}
        {!! Form::text('date', $date, ['class'=>'form-control','required']) !!}
    </div>

    <div class="form-group">
        {!! Form::submit(trans('app.filter'), ['class'=>'btn btn-primary']) !!}
        {!! link_to_route('receipts.index', 'Hari Ini', [], ['class'=>'btn btn-default']) !!}
    </div>
    {!! Form::close() !!}
</div>
<h2 class="page-header">
    {!! trans('receipt.index_page_title', ['date' => $date, 'count' => $receipts->total()]) !!}
</h2>

@include('receipts.partials.receipt-index')

{!! str_replace('/?', '?', $receipts->appends(Request::except('page'))->render()) !!}

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
    $('#date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect:true,
    });
})();
</script>
@endsection