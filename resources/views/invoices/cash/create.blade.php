@extends('layouts.app')

@section('title', trans('cash_invoice.create'))

@section('content')
<?php $date = Carbon\Carbon::now(); ?>

{!! Form::open(['route' => ['invoices.cash.store']]) !!}
@include('invoices.partials.cash-receipt-lists', ['receipts' => $unInvoicedReceipts])
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                {!! FormField::text('date',[
                    'label' => trans('app.date'),
                    'value' => $date->format('Y-m-d'),
                    'class' =>'date-select',
                    'required' => true,
                ]) !!}
            </div>
            <div class="col-md-3">
                {!! FormField::textarea('notes',['label'=> trans('app.notes'),'rows' => 4]) !!}
            </div>
        </div>
    </div>
    <div class="panel-footer">
        {!! Form::submit(trans('cash_invoice.create'), ['class'=>'btn btn-primary']) !!}
        {!! link_to_route('invoices.cash.index', trans('app.cancel'), [], ['class'=>'btn btn-default']) !!}
    </div>
</div>

{!! Form::close() !!}

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
    $('.date-select').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true,
        scrollInput: false
    });

    $('#select-all').click(function () {
        $('.select-me').prop('checked', this.checked);
    });

    $('.receipt-list tr').click(function(event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

    $('.select-me').change(function () {
        var check = ($('.select-me').filter(":checked").length == $('.select-me').length);
        $('#select-all').prop("checked", check);
    });
})();
</script>
@endsection