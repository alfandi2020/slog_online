@extends('layouts.app')

@section('title', $pickup->number.' - '.trans('pickup.detail'))

@section('content')

<div class="pull-right">
    @include('services.pickups.partials.show-links')
</div>
<h2 class="page-header">{{ $pickup->number }} <small>{{ trans('pickup.detail') }}</small></h2>

<div class="row">
    <div class="col-md-4">
        @include('services.pickups.partials.detail')
        @include('services.pickups.partials.departure-detail')
    </div>
    <div class="col-md-8">
        @if ($pickup->notes)
        <div class="well well-sm"><strong>{{ trans('pickup.notes') }}</strong> : {{ $pickup->notes }}</div>
        @endif
        {{-- Send pickup --}}
        @can ('send', $pickup)
        <div class="panel panel-info" id="send-pickup">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.send') }}</h3></div>
            <div class="panel-body">
                {{ Form::model($pickup, ['route' => ['pickups.send', $pickup->id], 'method' => 'patch']) }}
                <div class="row">
                    <div class="col-sm-4">
                        {!! FormField::text('sent_at', [
                            'label' => trans('pickup.sent_at'),
                            'placeholder' => 'yyyy-mm-dd HH:ii',
                            'class' => 'time-select',
                            'required' => true,
                        ]) !!}
                    </div>
                    <div class="col-sm-4">
                        {!! FormField::text('start_km', ['label' => trans('pickup.start_km')]) !!}
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
                {!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
                {{ Form::submit(trans('pickup.send'), ['class' => 'btn btn-success']) }}
                {{ Form::close() }}
            </div>
        </div>
        @endcan
        <div class="panel panel-default">
            <div class="panel-heading">
                {{-- {{ link_to_route(
                    'pickups.show',
                    trans('app.edit'),
                    [$pickup, 'action' => 'edit_customer_pickup'],
                    ['class' => 'pull-right', 'id' => 'edit_customer_pickup', 'title' => trans('pickup.edit_data')]
                ) }} --}}
                <h3 class="panel-title">{{ trans('pickup.customer_data') }}</h3>
            </div>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="">{{ trans('app.table_no') }}</th>
                        <th class="col-md-4">{{ trans('customer.customer') }}</th>
                        <th class="col-md-1 text-center">{{ trans('receipt.receipt') }}</th>
                        <th class="col-md-1 text-center">{{ trans('app.pc') }}</th>
                        <th class="col-md-1 text-center">{{ trans('app.item') }}</th>
                        <th class="col-md-1 text-center">{{ trans('app.weight') }}</th>
                        <th class="col-md-4">{{ trans('app.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                        $receiptsTotal = 0;
                        $pcsTotal = 0;
                        $itemsTotal = 0;
                        $weightTotal = 0;
                    @endphp
                    @foreach($pickup->customers as $customerId => $pickupData)
                    @php
                        $customer = App\Entities\Customers\Customer::find($customerId);
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $customer->name }}</td>
                        <td class="text-center">{{ $receiptsCount = $pickupData['receipts_count'] }}</td>
                        <td class="text-center">{{ $pcsCount = $pickupData['pcs_count'] }}</td>
                        <td class="text-center">{{ $itemsCount = $pickupData['items_count'] }}</td>
                        <td class="text-center">{{ $weight = $pickupData['weight_total'] }}</td>
                        <td>{!! nl2br($pickupData['notes']) !!}</td>
                    </tr>
                    @php
                        $receiptsTotal += $receiptsCount;
                        $pcsTotal += $pcsCount;
                        $itemsTotal += $itemsCount;
                        $weightTotal += $weight;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">{{ trans('app.total') }}</th>
                        <th class="text-center">{{ $receiptsTotal }}</th>
                        <th class="text-center">{{ $pcsTotal }}</th>
                        <th class="text-center">{{ $itemsTotal }}</th>
                        <th class="text-center">{{ $weightTotal }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
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
