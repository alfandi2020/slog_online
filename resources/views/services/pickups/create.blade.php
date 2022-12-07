@extends('layouts.app')

@section('title', trans('pickup.create'))

@section('content')
<div class="row">
    <div class="col-md-6 col-lg-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.create') }}</h3></div>
            {!! Form::open(['route' => 'pickups.store']) !!}
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">{!! FormField::select('courier_id', $pickupCouriers, ['required' => true, 'label' => trans('pickup.courier')]) !!}</div>
                </div>
                {!! FormField::select('delivery_unit_id', $deliveryUnits, ['required' => true, 'label' => trans('pickup.delivery_unit')]) !!}
                {!! FormField::multiSelect('customer_ids', $customers, [
                    'label' => trans('pickup.customer_select'),
                    'placeholder' => false,
                    'required' => true,
                    'info' => [
                        'class' => 'text-danger small',
                        'text' => 'Dapat pilih lebih dari 1 Customer, <strong>maks 10 Customer</strong>.',
                    ]
                ]) !!}
                {!! FormField::textarea('notes', ['label' => trans('pickup.notes')]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('pickup.create'), ['class' => 'btn btn-success']) !!}
                {{ link_to_route('pickups.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
{!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}

@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
{!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true,
        scrollInput: false
    });
    $('#customer_ids').select2({
        placeholder: "{{ trans('pickup.customer_select') }}",
        maximumSelectionLength: 10
    });
    $('#courier_id').select2();
})()
</script>
@endsection
