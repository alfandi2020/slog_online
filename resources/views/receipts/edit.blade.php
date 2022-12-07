@inject('reference', 'App\Entities\References\Reference')
@extends('layouts.app')

@section('title', trans('receipt.edit').' - '.$receipt->number)

@section('content')
{{ Form::model($receipt, ['route' => ['receipts.update', $receipt->number], 'method' => 'patch']) }}
@inject('customer', 'App\Entities\Customers\Customer')
@inject('paymentTypes', 'App\Entities\Receipts\PaymentType')
<div class="panel panel-default">
    <div class="panel-body">
        <legend>{{ trans('receipt.detail') }}</legend>
        <div class="row">
            <div class="col-md-3">
                {!! FormField::select('customer_id', $customer->isActive()->orderBy('name')->pluck('name','id'), [
                    'label' => trans('customer.customer'),
                    'value' => request('customer_id', $receipt->customer_id),
                    'class' => 'select2',
                    'placeholder' => trans('customer.retail'),
                ]) !!}
                {!! FormField::textDisplay(trans('receipt.number'), $receipt->number) !!}
            </div>
            <div class="col-md-3">
                {!! FormField::textDisplay(trans('receipt.dest_city'), $receipt->destCityName()) !!}
                {!! FormField::textDisplay(trans('receipt.dest_district'), $receipt->destDistrictName()) !!}
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="payment-type">
                        {!! FormField::radios('payment_type_id', $paymentTypes->toArray(), [
                            'label' => trans('receipt.payment_type'),
                            'value' => $receipt->payment_type_id ?: 1,
                            'required' => true,
                        ]) !!}
                        </div>
                        <div style="margin-top:28px"></div>
                        {!! FormField::text('reference_no', ['label' => trans('receipt.reference_no')]) !!}
                    </div>
                    <div class="col-sm-5">
                        {!! FormField::textarea('notes', [
                            'label' => trans('receipt.notes'),
                            'class' => 'text-right',
                            'value' => $receipt->notes,
                            'rows' => 5,
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <fieldset class="col-md-7">
                        {!! FormField::radios('pack_type_id', $reference::whereCat('pack_type')->pluck('name','id')->all(), [
                            'label' => trans('receipt.pack_type'),
                            'list_style' => 'unstyled',
                            'value' => $receipt->pack_type_id ?: 1,
                            'required' => true,
                        ]) !!}
                        {!! FormField::text('charged_weight', [
                            'label' => trans('receipt.charged_weight'),
                            'addon' => ['after' => 'Kg'],
                            'class' => 'text-right',
                            'value' => $receipt->getChargedWeight() ?: $receipt->charged_weight ?: 1,
                            'required' => true,
                        ]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                {!! FormField::text('pcs_count', [
                                    'label' => trans('receipt.pcs_count'),
                                    'addon' => ['after' => 'Koli'],
                                    'class' => 'text-right',
                                    'value' => $receipt->itemsCount() ?: $receipt->items_count ?: 1,
                                    'required' => true,
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! FormField::text('items_count', [
                                    'label' => trans('receipt.items_count'),
                                    'addon' => ['after' => 'Dus'],
                                    'class' => 'text-right',
                                    'value' => $receipt->itemsCount() ?: $receipt->items_count ?: 1,
                                    'required' => true,
                                ]) !!}
                            </div>
                        </div>
                        {!! FormField::textarea('pack_content', [
                            'label' => trans('receipt.pack_content'),
                            'class' => 'text-right',
                            'value' => $receipt->pack_content,
                            'rows' => 5,
                        ]) !!}
                    </fieldset>
                    <fieldset class="col-md-5">
                        {!! FormField::price('base_charge', [
                            'label' => trans('receipt.base_charge'),
                            'value' => $receipt->costs_detail['base_charge'] ?: 0,
                        ]) !!}
                        {!! FormField::price('discount', [
                            'label' => trans('receipt.discount'),
                            'value' => $receipt->costs_detail['discount'] ?: 0,
                        ]) !!}
                        {!! FormField::price('packing_cost', [
                            'label' => trans('receipt.packing_cost'),
                            'value' => $receipt->costs_detail['packing_cost'] ?: 0,
                        ]) !!}
                        {!! FormField::price('insurance_cost', [
                            'label' => trans('receipt.insurance_cost'),
                            'value' => $receipt->costs_detail['insurance_cost'] ?: 0,
                        ]) !!}
                        {!! FormField::price('add_cost', [
                            'label' => trans('receipt.add_cost'),
                            'value' => $receipt->costs_detail['add_cost'] ?: 0,
                        ]) !!}
                        {!! FormField::price('admin_fee', [
                            'label' => trans('receipt.admin_fee'),
                            'value' => $receipt->costs_detail['admin_fee'] ?: 0,
                        ]) !!}
                    </fieldset>
                </div>

                {!! FormField::text('customer_invoice_no', [
                    'label' => trans('receipt.customer_invoice_no'),
                    'info' => [
                        'text' => 'Beberapa No. Faktur/DO pisahkan dengan tanda koma. <br>Contoh: 75539891, 75539897',
                        'class' => 'text-info small',
                    ],
                    'value' => $receipt->customer_invoice_no,
                ]) !!}
            </div>
        </div>
        {!! html_link_to_route(
            'receipts.delete',
            trans('receipt.delete'),
            [$receipt->number],
            ['id' => 'del-receipt-'.$receipt->id, 'class' => 'btn btn-danger', 'icon' => 'trash']
        ) !!}
    </div>
    <div class="col-sm-7">
        <div class="panel panel-default">
            <div class="panel-body">
                <legend>Pickup Kiriman</legend>
                <div class="row">
                    <div class="col-md-6">
                        {!! FormField::text('pickup_time', [
                            'label' => trans('receipt.pickup_time'),
                            'class' => 'time-select',
                            'value' => $receipt->pickup_time ? $receipt->pickup_time->format('Y-m-d H:i') : date('Y-m-d H:i'),
                            'required' => true,
                        ]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! FormField::select('pickup_courier_id', $availableCourierList, [
                            'label' => trans('receipt.pickup_courier'),
                            'info' => [
                                'text' => 'Kurir yang pickup kiriman/paket ini.',
                                'class' => 'text-info small',
                            ],
                            'value' => $receipt->pickup_courier_id,
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
        @include('receipts.partials.form-consignor-consignee', ['submitValue' => trans('receipt.update')])
    </div>
</div>
{{ Form::close() }}
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
    $('.select2').select2();
    $('.time-select').datetimepicker({
        format:'Y-m-d H:i',
        closeOnTimeSelect: true
    });

    $('#customer_id').change(function(){
        $(this).find(':selected').each(function(){
            var optionValue = $(this).val();
            console.log(optionValue);
            if((optionValue == 37) || (optionValue == 182) || (optionValue == 183) || (optionValue == 184) || (optionValue == 185) || (optionValue == 186) || (optionValue == 187) || (optionValue == 188) || (optionValue == '')){
                $('.payment-type ul li:nth-child(2)').hide();
                $("#consignor_name").attr('readonly',false);
                // $("input[name='consignor_address[1]']").attr('readonly',false);
                // $("input[name='consignor_address[2]']").attr('readonly',false);
                // $("input[name='consignor_address[3]']").attr('readonly',false);
                $('#payment_type_id_1').prop('checked',true);
                $('#payment_type_id_2').prop('checked',false);
                console.log(optionValue);
            } else{
                $('.payment-type ul li:nth-child(2)').show();
                $("#consignor_name").attr('readonly',true);
                // $("input[name='consignor_address[1]']").attr('readonly',true);
                // $("input[name='consignor_address[2]']").attr('readonly',true);
                // $("input[name='consignor_address[3]']").attr('readonly',true);
                $('#payment_type_id_2').prop('checked',true);
                $('#payment_type_id_1').prop('checked',false);
            }
        });
    }).change();
})();
</script>
@endsection
