@inject('customer', 'App\Entities\Customers\Customer')
@inject('paymentTypes', 'App\Entities\Receipts\PaymentType')
<div class="panel panel-default">
    <div class="panel-body">
        <legend>Data Resi</legend>
        <div class="row">
            <div class="col-md-3">
                {!! FormField::select('customer_id', $customer->isActive()->pluck('name','id'), [
                    'label' => trans('customer.customer'),
                    'value' => request('customer_id', $receipt->customer_id),
                    'class' => 'select2',
                    // 'placeholder' => trans('customer.retail'),
                    'required' => true,
                ]) !!}
                {!! FormField::text('number', [
                    'label' => trans('receipt.number'),
                    'info' => [
                        'text' => trans('receipt.number_form_note'),
                        'class' => 'text-info small',
                    ],
                    'value' => $receipt->number,
                ]) !!}
            </div>
            <div class="col-md-3">
                {{ Form::hidden('orig_city_id', $receipt->orig_city_id, ['id' => 'orig_city_id']) }}
                {{ Form::hidden('orig_district_id', $receipt->orig_district_id, ['id' => 'orig_district_id']) }}
                {!! FormField::select('dest_city_id', $destinationCities, [
                    'label' => trans('rate.dest_city'),
                    'value' => request('dest_city_id', $receipt->dest_city_id),
                    'class' => 'select2',
                    'required' => true,
                ]) !!}
                {!! FormField::select('dest_district_id', $destinationDistricts, [
                    'label' => trans('rate.dest_district'),
                    'value' => request('dest_district_id', $receipt->dest_district_id),
                    'class' => 'select2',
                ]) !!}
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
                        {!! FormField::text('customer_invoice_no', [
                            'label' => trans('receipt.customer_invoice_no'),
                            'info' => [
                                'text' => 'Beberapa No. Faktur/DO pisahkan dengan tanda koma. <br>Contoh: 75539891, 75539897',
                                'class' => 'text-info small',
                            ],
                            'value' => $receipt->customer_invoice_no,
                        ]) !!}
                    </div>
                    <div class="col-sm-5">
                        {!! FormField::textarea('notes', [
                            'label' => trans('receipt.notes'),
                            'class' => 'text-right',
                            'value' => $receipt->notes,
                            'rows' => 5,
                        ]) !!}
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
    </div>
</div>
