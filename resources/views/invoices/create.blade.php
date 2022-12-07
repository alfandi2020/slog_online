@extends('layouts.app')

@section('title', trans('invoice.create'))

@section('content')
<?php $date = Carbon\Carbon::now(); ?>
<div class="pull-right">
    {!! link_to_route('customers.un-invoiced-receipts', trans('app.back_to') . ' ' . trans('receipt.un_invoiced'), [
        $customer->id
    ], ['class'=>'btn btn-default']) !!}
</div>
<h2 class="page-header">{{ trans('invoice.create') }} <small>{{ $customer->present()->numberName }}</small></h2>

{!! Form::open(['route' => ['invoices.store', $customer->id]]) !!}
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                {!! FormField::textDisplay(trans('invoice.to'), $customer->present()->numberName) !!}
                <div class="row">
                    <div class="col-md-4">
                        {!! FormField::text('periode', [
                            'label' => trans('invoice.periode'),
                            'value' => $date->format('Y-m')
                        ]) !!}
                    </div>
                    <div class="col-md-4">
                        {!! FormField::text('date',[
                            'label' => trans('app.date'),
                            'value' => $date->format('Y-m-d'),
                            'class' =>'date-select'
                        ]) !!}
                    </div>
                    <div class="col-md-4">
                        {!! FormField::text('end_date',[
                            'label' => trans('invoice.end_date'),
                            'value' => $date->addDays(Option::get('default_invoice_days', 30))->format('Y-m-d'),
                            'class' =>'date-select'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                {!! FormField::price('discount', [
                    'label' => trans('invoice.discount'),
                ]) !!}
                {!! FormField::price('admin_fee', [
                    'label' => trans('invoice.admin_fee'),
                    'value' => Option::get('default_invoice_admin_fee')
                ]) !!}
            </div>
            <div class="col-md-3">
                {!! FormField::textarea('notes',['label'=> trans('app.notes'),'rows' => 4]) !!}
            </div>
        </div>
    </div>
</div>
@include('invoices.partials.receipt-lists', ['receipts' => $customer->invoiceReadyReceipts])

<div class="panel-footer">
    {!! Form::hidden('account_no', $customer->account_no) !!}
    {!! Form::hidden('customer_id', $customer->id) !!}
    {!! Form::submit(trans('invoice.create'), ['class'=>'btn btn-primary']) !!}
    {!! link_to_route('customers.index', trans('app.cancel'), [], ['class'=>'btn btn-default']) !!}
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