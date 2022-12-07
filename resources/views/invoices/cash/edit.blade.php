@extends('layouts.app')

@section('title', trans('cash_invoice.edit') . ' ' . $invoice->number)

@section('content')

{!! Form::model($invoice, ['route'=>['invoices.cash.update', $invoice->id], 'method' => 'patch']) !!}
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                {!! FormField::text('date',[
                    'label' => trans('app.date'),
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
        {!! Form::submit(trans('invoice.update'), ['class'=>'btn btn-success']) !!}
        {!! link_to_route('invoices.cash.show', trans('app.cancel'), [$invoice->id], ['class'=>'btn btn-default']) !!}
    </div>
</div>
{!! Form::close() !!}

@include('invoices.partials.cash-receipt-lists', ['receipts' => $invoice->receipts, 'receiptPicker' => false])
<div class="text-right">
    {!! FormField::delete(
        [
            'route'=>['invoices.cash.destroy',$invoice->id],
            'class' => '',
            'onsubmit' => 'Anda yakin akan menghapus Invoice Tunai ('.$invoice->number.') ini?',
        ],
        trans('invoice.delete'),
        ['class'=>'btn btn-danger'],
        ['invoice_id'=>$invoice->id]
    ) !!}
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
})();
</script>
@endsection
