@extends('layouts.app')

@section('title', trans('pickup.receive').' - '.$pickup->number)

@section('content')
<div class="pull-right">
    {{ link_to_route('pickups.show', trans('pickup.back_to_show'), [$pickup], ['class' => 'btn btn-default']) }}
</div>
<h2 class="page-header">{{ $pickup->number }} <small>{{ trans('pickup.receive') }}</small></h2>

<div class="row">
    <div class="col-md-4">
        @include('services.pickups.partials.detail')
        @include('services.pickups.partials.departure-detail')
    </div>
    <div class="col-md-8">
        @if ($pickup->notes)
        <div class="well well-sm"><strong>{{ trans('pickup.notes') }}</strong> : {{ $pickup->notes }}</div>
        @endif
        <div class="panel panel-success">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.customer_data') }}</h3></div>
            {!! Form::model($pickup, [
                'route' => ['pickups.receive', $pickup->id],
                'method' => 'patch',
                'onsubmit' => 'return confirm("Periksa kembali detail pickup customer dan waktu kembali Kurir. Klik OK jika sudah yakin dan mengubah status pickup ini menjadi \"Sudah Kembali\".")'
            ]) !!}
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th class="">{{ trans('app.table_no') }}</th>
                        <th class="col-md-4">{{ trans('customer.customer') }}</th>
                        <th class="col-md-1 text-center" title="{{ trans('pickup.receipts_count') }}">{{ trans('app.receipt') }}</th>
                        <th class="col-md-1 text-center" title="{{ trans('pickup.pcs_count') }}">{{ trans('app.pc') }}</th>
                        <th class="col-md-1 text-center" title="{{ trans('pickup.items_count') }}">{{ trans('app.item') }}</th>
                        <th class="col-md-1 text-center" title="{{ trans('pickup.weight_total') }}">{{ trans('app.weight') }}</th>
                        <th class="col-md-4">{{ trans('app.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach($pickup->customers as $customerId => $pickupData)
                    @php
                        $customer = App\Entities\Customers\Customer::find($customerId);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>
                            {!! FormField::text(
                                'receipts_count['.$customerId.']',
                                [
                                    'type' => 'number',
                                    'label' => false,
                                    'value' => $pickupData['receipts_count'],
                                    'required' => true,
                                    'min' => 0,
                                ]
                            ) !!}
                        </td>
                        <td>
                            {!! FormField::text(
                                'pcs_count['.$customerId.']',
                                [
                                    'type' => 'number',
                                    'label' => false,
                                    'value' => $pickupData['pcs_count'],
                                    'required' => true,
                                    'min' => 0,
                                ]
                            ) !!}
                        </td>
                        <td>
                            {!! FormField::text(
                                'items_count['.$customerId.']',
                                [
                                    'type' => 'number',
                                    'label' => false,
                                    'value' => $pickupData['items_count'],
                                    'required' => true,
                                    'min' => 0,
                                ]
                            ) !!}
                        </td>
                        <td>
                            {!! FormField::text(
                                'weight_total['.$customerId.']',
                                [
                                    'type' => 'number',
                                    'label' => false,
                                    'value' => $pickupData['weight_total'],
                                    'required' => true,
                                    'min' => 0,
                                ]
                            ) !!}
                        </td>
                        <td>
                            {!! FormField::text(
                                'notes['.$customerId.']',
                                [
                                    'label' => false,
                                    'value' => $pickupData['notes'],
                                    'placeholder' => trans('pickup.notes')
                                ]
                            ) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        {!! FormField::text('sent_at', [
                            'label' => trans('pickup.sent_at'),
                            'placeholder' => 'yyyy-mm-dd HH:ii',
                            'value' => $pickup->sent_at->format('Y-m-d H:i'),
                            'class' => 'time-select',
                            'required' => true,
                        ]) !!}
                    </div>
                    <div class="col-sm-3">
                        {!! FormField::text('start_km', ['label' => trans('pickup.start_km')]) !!}
                    </div>
                    <div class="col-sm-3">
                        {!! FormField::text('returned_at', [
                            'label' => trans('pickup.returned_at'),
                            'placeholder' => 'yyyy-mm-dd HH:ii',
                            'value' => optional($pickup->returned_at)->format('Y-m-d H:i'),
                            'class' => 'time-select',
                            'required' => true,
                        ]) !!}
                    </div>
                    <div class="col-sm-3">
                        {!! FormField::text('end_km', ['label' => trans('pickup.end_km')]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! FormField::select('courier_id', $pickupCouriers, ['required' => true, 'label' => trans('pickup.courier')]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! FormField::select('delivery_unit_id', $deliveryUnits, ['required' => true, 'label' => trans('pickup.delivery_unit')]) !!}
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('pickup.receive'), ['class' => 'btn btn-success']) !!}
                {{ link_to_route('pickups.show', trans('app.cancel'), [$pickup], ['class' => 'btn btn-default']) }}
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
    $('.time-select').datetimepicker({
        format:'Y-m-d H:i',
        closeOnTimeSelect: true
    });
    $('#courier_id,#delivery_unit_id').select2();
})();
</script>
@endsection
