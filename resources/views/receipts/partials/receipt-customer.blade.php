@can('edit-customer', $receipt)
    @inject('customer', 'App\Entities\Customers\Customer')
    @inject('paymentTypes', 'App\Entities\Receipts\PaymentType')
@endcan

<div class="panel panel-default">
    <div class="panel-heading">

        @can('edit-customer', $receipt)
            <div class="pull-right"><a data-toggle="modal" style="cursor: pointer;" data-target="#customerEdit">{{ trans('app.edit') }}</a></div>
        @endcan

        <h3 class="panel-title">{{ trans('customer.customer') }}</h3>
    </div>
    @if (!is_null($receipt->customer))
    <table class="table table-condensed">
        <tbody>
            <tr><th>{{ trans('customer.account_no') }}</th><td>{{ $receipt->customer->present()->numberLink() }}</td></tr>
            <tr><th>{{ trans('app.name') }}</th><td>{{ $receipt->customer->present()->nameLink() }}</td></tr>
            <tr><th>{{ trans('app.code') }}</th><td>{{ $receipt->customer->code }}</td></tr>
            <tr><th>{{ trans('customer.comodity') }}</th><td>{{ $receipt->customer->comodity->name }}</td></tr>
        </tbody>
    </table>
    @else
    <div class="panel-body text-center"><span class="lead">Customer Umum</span></div>
    @endif
</div>

@can('edit-customer', $receipt)
<div class="modal fade" id="customerEdit" role="dialog" aria-labelledby="customerEditLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="customerEditLabel">{{ trans('customer.edit') }}</h4>
            </div>
            {{ Form::open(['route' => ['receipts.customer-update.store', $receipt->number], 'method' => 'patch']) }}
            <div class="modal-body">
                {!! FormField::select('customer_id', $customer->isActive()->orderBy('name')->pluck('name','id'), [
                    'label' => trans('customer.customer'),
                    'value' => request('customer_id', $receipt->customer_id),
                    'class' => 'select2',
                    'placeholder' => trans('customer.retail'),
                    'style' => 'width:300px'
                ]) !!}
                <div class="payment-type">
                {!! FormField::radios('payment_type_id', $paymentTypes->toArray(), [
                    'label' => trans('receipt.payment_type'),
                    'value' => $receipt->payment_type_id ?: 1,
                    'required' => true,
                ]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {{ Form::submit(trans('receipt.customer_update'), ['class' => 'btn btn-warning']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endcan

@can('edit-customer', $receipt)
    @section('ext_css')
    {!! Html::style(url('css/plugins/select2.min.css')) !!}
    @endsection

    @push('ext_js')
    {!! Html::script(url('js/plugins/select2.min.js')) !!}
    @endpush

    @section('script')
    <script>
    (function() {
        $('.select2').select2();

        $('#customer_id').change(function(){
        $(this).find(':selected').each(function(){
            var optionValue = $(this).val();
            console.log(optionValue);
            if((optionValue == 37) || (optionValue == 182) || (optionValue == 183) || (optionValue == 184) || (optionValue == 185) || (optionValue == 186) || (optionValue == 187) || (optionValue == 188) || (optionValue == '')){
                $('.payment-type ul li:nth-child(2)').hide();
                $('#payment_type_id_1').prop('checked',true);
                $('#payment_type_id_2').prop('checked',false);
                console.log(optionValue);
            } else{
                $('.payment-type ul li:nth-child(2)').show();
                $('#payment_type_id_2').prop('checked',true);
                $('#payment_type_id_1').prop('checked',false);
            }
        });
    }).change();
    })();
    </script>
    @endsection
@endcan
