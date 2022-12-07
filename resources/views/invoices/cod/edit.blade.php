@extends('layouts.app')

@section('title', trans('cod_invoice.edit') . ' ' . $invoice->number)

@section('content')

@include('invoices.partials.cod-invoice-stat')

@if ($invoice->notes)
    <p class="well well-sm"><strong>{{ trans('app.notes') }} :</strong> {{ $invoice->notes }}</p>
@endif

<div class="row">
    <div class="col-md-4 col-lg-offset-4">
        {!! Form::model($invoice, ['route'=>['invoices.cod.update', $invoice->id], 'method' => 'patch']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('cod_invoice.edit') }} - {{ $invoice->number }}</h3></div>
            <div class="panel-body">
                {!! FormField::text('date',[
                    'label' => trans('app.date'),
                    'class' =>'date-select',
                    'required' => true,
                ]) !!}
                {!! FormField::textarea('notes',['label'=> trans('app.notes'),'rows' => 4]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('invoice.update'), ['class'=>'btn btn-success']) !!}
                {!! link_to_route('invoices.cod.show', trans('app.cancel'), [$invoice->id], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
        {!! Form::close() !!}

        <div class="text-right">
            {!! FormField::delete(
                [
                    'route'=>['invoices.cod.destroy',$invoice->id],
                    'class' => '',
                    'onsubmit' => 'Anda yakin akan menghapus Invoice Tunai ('.$invoice->number.') ini?',
                ],
                trans('invoice.delete'),
                ['class'=>'btn btn-danger'],
                ['invoice_id'=>$invoice->id]
            ) !!}
        </div>

    </div>
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
