@extends('layouts.app')

@section('title', trans('cod_invoice.create'))

@section('content')
<?php $date = Carbon\Carbon::now(); ?>

{!! Form::open(['route' => ['invoices.cod.store']]) !!}

<div class="row">
    <div class="col-md-4 col-lg-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('cod_invoice.create') }}</h3></div>
            <div class="panel-body">
                {!! FormField::text('date',[
                    'label' => trans('app.date'),
                    'value' => $date->format('Y-m-d'),
                    'class' =>'date-select',
                    'required' => true,
                ]) !!}

                {!! FormField::textarea('notes',['label'=> trans('app.notes'),'rows' => 4]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('cod_invoice.create'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('invoices.cod.index', trans('app.cancel'), [], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
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