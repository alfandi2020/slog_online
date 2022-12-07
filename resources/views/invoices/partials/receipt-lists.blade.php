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
    <table class="table table-condensed table-hover receipt-list">
        <thead>
            <tr>
                <th class="text-center">{{ trans('app.table_no') }}</th>
                <th class="text-center">{{ trans('app.date') }}</th>
                <th class="text-center">{{ trans('receipt.number') }}</th>
                <th class="text-center">{{ trans('receipt.consignee') }}</th>
                <th class="text-center">{{ trans('receipt.destination') }}</th>
                <th class="text-center">{{ trans('service.service') }}</th>
                <th class="text-center">{{ trans('receipt.qty') }}</th>
                <th class="text-center">{{ trans('receipt.weight') }}</th>
                <th class="text-center">{{ trans('receipt.status') }}</th>
                <th class="text-right">{{ trans('receipt.base_rate') }}</th>
                <th class="text-right">{{ trans('receipt.total') }}</th>
                @if ($canEditReceipt)
                <th class="text-center">{{ trans('app.action') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <?php
                $itemCountTotal = 0;
                $itemWeightTotal = 0;
                $billAmountTotal = 0;
            ?>
            @forelse($receipts as $key => $receipt)
            @php
                $class = '';
                // if (session('last_updated_id') == $receipt->id) {
                //     $class = 'class=success';
                // } elseif ($receipt->rate->customer_id != $receipt->customer_id) {
                //     $class = 'style=background-color:#faf2cc';
                // }
            @endphp
            <tr {{ $class }} id="{{ $receipt->id }}">
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
                <td class="text-center">{{ link_to_route('receipts.show', $receipt->number, [$receipt->number], ['target' => '_blank']) }}</td>
                <td class="text-center">{{ $receipt->consignee['name'] }}</td>
                <td class="text-center">{{ $receipt->destinationName() }}</td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td class="text-center">{{ $receipt->items_count }}</td>
                <td class="text-center">{{ $receipt->weight }}</td>
                <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
                <td class="text-right">{{ formatRp($receipt->base_rate) }}</td>
                <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
                @if ($canEditReceipt)
                <td class="text-center">
                    {{ link_to_route('invoices.edit', trans('receipt.cost_edit'), [
                        $invoice->id, 'action' => 'receipt_edit', 'editable_receipt_id' => $receipt->id
                    ], ['id' => 'edit-receipt-cost-' . $receipt->id, 'class' => 'btn btn-warning btn-xs', 'title' => 'Edit Harga Resi']) }}
                </td>
                @endif
            </tr>
            <?php
                $itemCountTotal += $receipt->items_count;
                $itemWeightTotal += $receipt->weight;
                $billAmountTotal += $receipt->bill_amount;
            ?>
            @empty
            <tr><td colspan="12">{{ trans('receipt.no_receipts') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center" colspan="6">{{ trans('app.total') }}</th>
                <th class="text-center">{{ $itemCountTotal }}</th>
                <th class="text-center">{{ $itemWeightTotal }}</th>
                <th class="text-right" colspan="2">{{ trans('invoice.receipts_bill_amount') }}</th>
                <th class="text-right">{{ formatRp($billAmountTotal) }}</th>
                @if ($canEditReceipt)
                <th></th>
                @endif
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