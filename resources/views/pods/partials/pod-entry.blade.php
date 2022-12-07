@inject('podStatusCode', 'App\Entities\Receipts\Status')
{!! Form::open(['route' => ['pods.store', $receipt->id], 'enctype'=>'multipart/form-data']) !!}
{{ Form::hidden('manifest_id', $manifest->id) }}
<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Entry PDO {{ trans('receipt.receipt') }}</h3></div>
    <table class="table">
        <tr>
            <td>{!! Form::label('number', trans('receipt.number'), ['class'=>'control-label']) !!}</td>
            <td>{!! Form::text('number', $receipt->number, ['class'=>'form-control','readonly']) !!}</td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="row">
                    <div class="col-md-6">
                        @if(Auth::user()->isCourier())
                        {!! Form::label('delivery_courier_id', trans('manifest.courier'), ['class'=>'control=label']) !!}
                        {!! Form::text('delivery_courier', auth()->user()->name, ['class'=>'form-control','readonly']) !!}
                        {!! Form::hidden('delivery_courier_id', $manifest->handler_id) !!}
                        @else
                        {!! FormField::select('delivery_courier_id', $couriers, ['label' => trans('manifest.courier'), 'value' => $manifest->handler_id]) !!}
                        @endif
                    </div>
                    <div class="col-md-6">
                        {!! FormField::text('time', ['label' => trans('app.time'), 'value' => date('Y-m-d H:i')]) !!}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="row">
                    <div class="col-md-6">
                        {!! FormField::select('status_code', $podStatusCode::podDropdown(), ['label' => trans('app.status'), 'placeholder' => false]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! FormField::text('recipient', ['label' => trans('receipt.consignee_name')]) !!}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>{!! Form::label('notes', trans('app.notes'), ['class'=>'control-label']) !!}</td>
            <td>
                {!! Form::textarea('notes', null, ['class'=>'form-control','rows' => 3]) !!}
                {!! $errors->first('notes', '<span class="form-error">:message</span>') !!}
            </td>
        </tr>
        <tr>
            <td>{!! Form::label('customer_invoice_no', trans('receipt.customer_invoice_no'), ['class'=>'control-label']) !!}</td>
            <td>
                {{ Form::text('customer_invoice_no', $receipt->customer_invoice_no, ['class'=>'form-control']) }}
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
        {!! Form::submit(trans('app.submit'), ['class'=>'btn btn-primary']) !!}
        @if (isset($returnLink))
            {{ $returnLink }}
        @else
            {{ link_to_route('pods.by-manifest', trans('app.cancel'), [
                'manifest_number' => $manifest->number
            ], ['class' => 'btn btn-default pull-right']) }}
        @endif
    </div>
</div>
{!! Form::close() !!}
