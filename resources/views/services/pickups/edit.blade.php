@extends('layouts.app')

@section('title', trans('pickup.edit'))

@section('content')
@if (request('action') == 'delete')
<div class="row">
    <div class="col-md-4 col-lg-offset-3">
        @include('services.pickups.delete')
    </div>
</div>

@else

<div class="row">
    <div class="col-md-6 col-lg-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.edit') }} {{ $pickup->number }}</h3></div>
            {!! Form::model($pickup, ['route' => ['pickups.update', $pickup->id],'method' => 'patch']) !!}
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">{!! FormField::select('courier_id', $pickupCouriers, ['required' => true, 'label' => trans('pickup.courier')]) !!}</div>
                </div>
                {!! FormField::select('delivery_unit_id', $deliveryUnits, ['required' => true, 'label' => trans('pickup.delivery_unit')]) !!}
                {!! FormField::multiSelect('customer_ids', $customers, [
                    'label' => trans('pickup.customer_select'),
                    'placeholder' => false,
                    'required' => true,
                    'value' => array_keys($pickup->customers),
                    'info' => [
                        'class' => 'text-danger small',
                        'text' => 'Dapat pilih lebih dari 1 Customer, <strong>maks 10 Customer</strong>.',
                    ]
                ]) !!}
                {!! FormField::textarea('notes', ['label' => trans('pickup.notes')]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('pickup.update'), ['class' => 'btn btn-success']) !!}
                {{ link_to_route('pickups.show', trans('pickup.back_to_show'), [$pickup], ['class' => 'btn btn-default']) }}
                @if (request('action') != 'delete')
                    {{ link_to_route(
                        'pickups.edit',
                        trans('app.delete'),
                        [$pickup, 'action' => 'delete'],
                        ['id' => 'del-pickup-' . $pickup->id, 'class' => 'btn btn-danger pull-right']
                    ) }}
                @endif
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endif

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
