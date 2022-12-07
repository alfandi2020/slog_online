@inject('paymentTypes', 'App\Entities\Receipts\PaymentType')
<div class="panel panel-default">
    <div class="panel-body">
        <legend>Data Resi</legend>
        <div class="row">
            <div class="col-md-7">
                {!! FormField::text('number', [
                    'label' => trans('receipt.number'),
                    'info' => [
                        'text' => trans('receipt.number_form_note'),
                        'class' => 'text-info small',
                    ],
                    'value' => $receipt->number,
                ]) !!}
                {!! FormField::text('reference_no', ['label' => trans('receipt.reference_no')]) !!}
            </div>
            <div class="col-md-5">
                {!! FormField::text('pickup_time', [
                    'label' => trans('receipt.pickup_time'),
                    'class' => 'time-select',
                    'value' => $receipt->pickup_time ? $receipt->pickup_time->format('Y-m-d H:i') : date('Y-m-d H:i'),
                    'required' => true,
                ]) !!}
                <div class="payment-type">
                    {!! FormField::radios('payment_type_id', $paymentTypes->toArray(), [
                    'label' => trans('receipt.payment_type'),
                    'value' => $receipt->payment_type_id ?: 1,
                    'required' => true,
                ]) !!}
                </div>
            </div>
        </div>
        {!! FormField::select('pickup_courier_id', $availableCourierList, [
            'label' => trans('receipt.pickup_courier'),
            'info' => [
                'text' => 'Kurir yang pickup kiriman/paket ini.',
                'class' => 'text-info small',
            ],
            'value' => $receipt->pickup_courier_id,
        ]) !!}
        <div class="row">
            <div class="col-sm-6">
                {!! FormField::textarea('pack_content', [
                    'label' => trans('receipt.pack_content'),
                    'class' => 'text-right',
                    'value' => $receipt->pack_content,
                ]) !!}
            </div>
            <div class="col-sm-6">
                {!! FormField::textarea('notes', [
                    'label' => trans('receipt.notes'),
                    'class' => 'text-right',
                    'value' => $receipt->notes,
                ]) !!}
            </div>
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
