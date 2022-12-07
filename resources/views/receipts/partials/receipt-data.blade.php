@inject('paymentTypes', 'App\Entities\Receipts\PaymentType')
<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Data Resi</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th>{{ trans('receipt.number') }}</th><td>{{ $receipt->number }}</td></tr>
            <tr><th>{{ trans('app.status') }}</th><td>{!! $receipt->present()->statusLabel !!}</td></tr>
            <tr><th>{{ trans('service.service') }}</th><td>{{ strtoupper($receipt->service()) }}</td></tr>
            <tr><th>{{ trans('receipt.pickup_time') }}</th><td>{{ $receipt->pickup_time }}</td></tr>
            @if ($receipt->exists)
            <tr>
                <th>{{ trans('receipt.pickup_courier') }}</th>
                <td>
                    @if ($receipt->pickupCourier)
                    {{ $receipt->pickupCourier->name }}
                    @else
                    -
                    @endif
                </td>
            </tr>
            @else
            <tr>
                <th>{{ trans('receipt.pickup_courier') }}</th>
                <td>
                    @if ($receipt->pickup_courier_id)
                    @inject('user', 'App\Entities\Users\User')
                    {{ $user::findOrFail($receipt->pickup_courier_id)->name }}
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endif
            <tr>
                <th>Pembayaran</th>
                <td>
                    <span class="badge">{{ $paymentTypes::getNameById($receipt->payment_type_id) }}</span>
                </td>
            </tr>
            <tr><th>{{ trans('receipt.reference_no') }}</th><td>{{ $receipt->reference_no }}</td></tr>
            <tr><th>{{ trans('receipt.customer_invoice_no') }}</th><td>{{ $receipt->customer_invoice_no }}</td></tr>
            @if ($receipt->exists)
            <tr><th>{{ trans('receipt.network') }}</th><td>{{ $receipt->present()->creatorNetwork }}</td></tr>
            <tr><th>{{ trans('app.created_by') }}</th><td>{{ $receipt->creator->name }}</td></tr>
            <tr><th>{{ trans('invoice.number') }}</th><td>{{ $receipt->getInvoiceLink() }}</td></tr>
            @else
            <tr><th>{{ trans('receipt.network') }}</th><td>{{ auth()->user()->present()->networkName }}</td></tr>
            <tr><th>{{ trans('app.created_by') }}</th><td>{{ auth()->user()->name }}</td></tr>
            @endif
            <tr><th>{{ trans('receipt.notes') }}</th><td>{{ $receipt->notes }}</td></tr>
        </tbody>
    </table>
</div>
