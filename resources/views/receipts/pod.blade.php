@extends('layouts.receipt-detail')

@section('subtitle', trans('pod.pod_full'))

@section('receipt-content')
@if ($receipt->proof)
<div class="row">
    <div class="col-sm-4 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                @can('edit-pod', $receipt)
                @if (request('action') != 'edit')
                <div class="pull-right">
                    {{ link_to_route('receipts.pod', 'Edit', [$receipt->number, 'action' => 'edit']) }}
                </div>
                @endif
                @endcan
                <h3 class="panel-title">{{ trans('pod.show') }}</h3>
            </div>
            <table class="table table-condensed">
                <tbody>
                    <tr><th>{{ trans('app.status') }}</th><td><span class="label label-success">{{ __('receipt_status.'.$receipt->proof->status_code) }}</span></td></tr>
                    <tr><th>{{ trans('receipt.consignee_name') }}</th><td>{{ $receipt->proof->recipient }}</td></tr>
                    <tr><th>{{ trans('app.time') }}</th><td>{{ $receipt->proof->delivered_at }}</td></tr>
                    <tr><th>{{ trans('receipt.delivery_courier') }}</th><td>{{ $receipt->deliveryCourier->name }}</td></tr>
                    <tr><th>{{ trans('app.notes') }}</th><td>{{ $receipt->proof->notes }}</td></tr>
                    {{-- @if( $receipt['image_proof'] !== '')
                    <tr><th>{{ trans('Bukti Foto') }}</th><td><a href="{{ asset('storage/imgs/pod/' . $receipt['image_proof'])  }}" target="_blank"><img src="{{ asset('storage/imgs/pod/' . $receipt['image_proof'])  }}" style="max-width: 200px; height: auto;"></a></td></tr>
                    @endif --}}
                </tbody>
            </table>
        </div>
    </div>
    @can('edit-pod', $receipt)
    @if (request('action') === 'edit')
    <div class="col-md-5">
        @inject('podStatusCode', 'App\Entities\Receipts\Status')
        {!! Form::open(['route' => ['pods.update', $receipt->number, $receipt->proof->id], 'method' => 'patch', 'enctype' => 'multipart/form-data']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Edit PDO {{ trans('receipt.receipt') }}</h3></div>
            <table class="table">
                <tr>
                    <td>{!! Form::label('number', trans('receipt.number'), ['class'=>'control-label']) !!}</td>
                    <td>{!! Form::text('number', $receipt->number, ['class'=>'form-control','readonly']) !!}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row">
                            <div class="col-md-6">
                                {!! FormField::select('delivery_courier_id', $couriers, ['label' => trans('manifest.courier'), 'value' => $receipt->delivery_courier_id]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! FormField::text('time', ['label' => trans('app.time'), 'value' => $receipt->proof->delivered_at->format('Y-m-d H:i')]) !!}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row">
                            <div class="col-md-6">
                                {!! FormField::select('status_code', $podStatusCode::podDropdown(), ['label' => trans('app.status'), 'placeholder' => false, 'value' => $receipt->status_code]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! FormField::text('recipient', ['label' => trans('receipt.consignee_name'), 'value' => $receipt->proof->recipient]) !!}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{!! Form::label('notes', trans('app.notes'), ['class'=>'control-label']) !!}</td>
                    <td>
                        {!! Form::textarea('notes', $receipt->proof->notes, ['class'=>'form-control','rows' => 3]) !!}
                        {!! $errors->first('notes', '<span class="form-error">:message</span>') !!}
                    </td>
                </tr>
                <tr>
                    <td>{!! Form::label('customer_invoice_no', trans('receipt.customer_invoice_no'), ['class'=>'control-label']) !!}</td>
                    <td>
                        {!! Form::text('customer_invoice_no', $receipt->customer_invoice_no, ['class'=>'form-control','rows' => 3]) !!}
                        {!! $errors->first('customer_invoice_no', '<span class="form-error">:message</span>') !!}
                    </td>
                </tr>
                {{-- <tr>
                    <td>{!! Form::label('image_proof', trans('Bukti Foto'), ['class'=>'control-label']) !!}</td>
                    <td>{!! Form::file('image_proof', ['class'=>'form-control']) !!}
                        {!! $errors->first('image_proof', '<span class="form-error">:message</span>') !!}
                    </td>
                </tr> --}}
            </table>
            <div class="panel-footer">
                {!! Form::submit(trans('app.update'), ['class'=>'btn btn-success']) !!}
                {{ link_to_route('receipts.pod', trans('app.cancel'), [
                    $receipt->number
                ], ['class' => 'btn btn-default pull-right']) }}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @endif
    @endcan
</div>
@else
    <div class="alert alert-warning">Belum ada POD untuk Resi ini</div>
@endif
@endsection

@if (request('action') === 'edit')
@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#time').datetimepicker({
        format:'Y-m-d H:i'
    });
})();
</script>
@endsection
@endif
