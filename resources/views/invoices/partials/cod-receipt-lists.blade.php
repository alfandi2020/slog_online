<?php
    $invoiceId = isset($invoiceId) ? $invoiceId : 'null';
    $receiptPicker = isset($receiptPicker) && $receiptPicker == false ? false : true;
    $canEditReceipt = isset($canEditReceipt) && $canEditReceipt == true ? true : false;
?>
<div class="panel panel-{{ $errors->has('receipt_id') ? 'danger' : 'default' }} table-responsive">
    <div class="panel-heading">
        {!! $errors->first('receipt_id', '<span class="form-error pull-right">:message</span>') !!}
        <h3 class="panel-title">{{ trans('receipt.list') }} (total : {{ $receipts->count() }})</h3>
    </div>
    @if ($receiptPicker)
    <div class="panel-body">Pilih/Centang Resi yang akan masuk Invoice COD.</div>
    @endif
    <table class="table table-condensed table-hover receipt-list">
        <thead>
            <tr>
                <th class="text-center">{{ trans('app.table_no') }}</th>
                <th class="text-center">{{ trans('app.date') }}</th>
                <th class="text-center">{{ trans('app.time') }}</th>
                <th class="text-center">{{ trans('receipt.number') }}</th>
                <th class="text-center">{{ trans('service.service') }}</th>
                <th class="text-center">{{ trans('receipt.payment_type') }}</th>
                <th>{{ trans('receipt.consignee') }}</th>
                <th class="text-right">{{ trans('receipt.admin_fee') }}</th>
                <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
                <th class="text-center">{{ trans('receipt.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $key => $receipt)
            <tr {{ session('last_updated_id') == $receipt->id ? 'class=success' : '' }}>
                <td class="text-center">
                    @if ($receiptPicker)
                    {!! Form::checkbox('receipt_id[' . $receipt->id . ']', $receipt->id, $receipt->invoice_id == $invoiceId, [
                        'class'=>'select-me',
                        'id' => 'select-receipt-' . $receipt->id
                    ]) !!}
                    @endif
                    {{ $key + 1 }}
                </td>
                <td class="text-center">{{ $receipt->pickup_time->format('Y-m-d') }}</td>
                <td class="text-center">{{ $receipt->pickup_time->format('H:i') }}</td>
                <td class="text-center">{{ link_to_route('receipts.show', $receipt->number, [$receipt->number], ['target' => '_blank']) }}</td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td class="text-center">{{ $receipt->payment_type }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td class="text-right">{{ formatRp($receipt->costs_detail['admin_fee']) }}</td>
                <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
                <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
            </tr>
            @empty
            <tr><td colspan="13">{{ trans('receipt.no_receipts') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="8">{{ trans('app.count') }}</th>
                <th class="text-right">{{ formatRp($receipts->sum('bill_amount')) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    @if ($receiptPicker)
    <table class="table">
        <tbody>
            <tr><td><label for="select-all" style="cursor:pointer"><input type="checkbox" id="select-all" /> Centang Semua</label></td></tr>
        </tbody>
    </table>
    @endif
</div>
