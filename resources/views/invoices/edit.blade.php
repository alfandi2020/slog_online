@extends('layouts.app')

@section('title', trans('invoice.edit') . ' ' . $invoice->number)

@section('content')

{!! Form::model($invoice, ['route'=>['invoices.update', $invoice->id], 'method' => 'patch']) !!}
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-8">
                        {!! FormField::textDisplay(trans('invoice.to'), $invoice->customer->present()->numberName) !!}
                    </div>
                    <div class="col-md-4">
                        {!! FormField::select(
                            'creator_id', $accountingUsersList,
                            ['label' => trans('invoice.creator')]
                        ) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">{!! FormField::text('periode', ['label' => trans('invoice.periode')]) !!}</div>
                    <div class="col-md-4">
                        {!! FormField::text('date',[
                            'label' => trans('app.date'),
                            'class' =>'date-select'
                        ]) !!}
                    </div>
                    <div class="col-md-4">
                        {!! FormField::text('end_date',[
                            'label' => trans('invoice.end_date'),
                            'class' =>'date-select'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                {!! FormField::text('discount', [
                    'label' => trans('invoice.discount'),
                    'addon' => ['before'=>'Rp.'],
                    'value' => $invoice->charge_details['discount']
                ]) !!}
                {!! FormField::text('admin_fee', [
                    'label' => trans('invoice.admin_fee'),
                    'addon' =>['before'=>'Rp.'],
                    'value' => $invoice->charge_details['admin_fee']
                ]) !!}
            </div>
            <div class="col-md-3">
                {!! FormField::textarea('notes',['label'=> trans('app.notes'),'rows' => 4]) !!}
            </div>
        </div>
    </div>
    <div class="panel-footer">
        {!! Form::submit(trans('invoice.update'), ['class'=>'btn btn-success']) !!}
        {!! link_to_route('invoices.show', trans('app.cancel'), [$invoice->id], ['class'=>'btn btn-default']) !!}
        {!! link_to_route('invoices.delete', trans('invoice.delete'), [$invoice->id], ['class'=>'btn btn-danger pull-right']) !!}
    </div>
</div>
{!! Form::close() !!}

@include('invoices.partials.form-add-remove-receipts', [
    'assignRoute' => 'invoices.assign-receipt',
    'removeRoute' => 'invoices.remove-receipt',
    'manifestId' => $invoice->id,
])

@include('invoices.partials.receipt-lists', ['receipts' => $invoice->receipts, 'receiptPicker' => false, 'canEditReceipt' => true])
@includeWhen(!is_null($editableReceipt), 'invoices.partials.receipt-cost-edit')
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
    @if (!is_null($editableReceipt))
    $('#myModal').modal('show');
    @endif
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

    var recalculateBasecharge = function() {
    var valueBaseRate = parseFloat($('input[name="base_rate"]').val())
    var valueWeight = parseFloat($('input[name="weight"]').val())

    valueBaseCharge = valueBaseRate * valueWeight || "Invalid number"
    $('input[name="base_charge"]').val(valueBaseCharge)
    }
    $('input[name="base_rate"]').keyup(function() { recalculateBasecharge(); });
    $('input[name="weight"]').keyup(function() { recalculateBasecharge(); });
})();
</script>
@endsection
