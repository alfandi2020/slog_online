<?php
$invoiceId = isset($invoiceId) ? $invoiceId : 'null';
$receiptPicker = isset($receiptPicker) && $receiptPicker == false ? false : true;
$canEditReceipt = isset($canEditReceipt) && $canEditReceipt == true ? true : false;
?>
<div class="panel panel-{{ $errors->has('receipt_id') ? 'danger' : 'default' }} table-responsive">
    <div class="panel-heading">
        {!! $errors->first('receipt_id', '<span style="color:red" class="pull-right">:message</span>') !!}

        <h3 class="panel-title">
            {{ trans('receipt.list') }} (total : {{ $receipts->count() }})
            <span style="color:red">Wajib dipilih</span>
        </h3>
    </div>
    <table class="table table-condensed table-hover receipt-list">
        <thead>
            <tr>
                <th class="text-center">{{ trans('app.table_no') }}</th>
                <th class="text-center">{{ trans('app.date') }}</th>
                <th class="text-center">{{ trans('receipt.number') }}</th>
                <th>{{ trans('receipt.consignee') }}</th>
                <th class="text-center">{{ trans('receipt.destination') }}</th>
                <th class="text-center">{{ trans('service.service') }}</th>
                <th class="text-center">{{ trans('receipt.status') }}</th>
                <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $itemCountTotal = 0;
            $itemWeightTotal = 0;
            $billAmountTotal = 0;
            ?>
            @forelse($receipts as $key => $receipt)
            <tr {{ session('last_updated_id') == $receipt->id ? 'class=success' : '' }}>
                <td class="text-center">
                    @if ($receiptPicker)
                    {!! Form::checkbox('receipt_id[' . $receipt->id . ']', $receipt->id, false, [
                        'class'=>'select-me',
                        'id' => 'select-receipt-' . $receipt->id
                    ]) !!}
                    @endif
                    {{ $key + 1 }}
                </td>
                <td class="text-center">{{ $receipt->pickup_time->format('Y-m-d') }}</td>
                <td class="text-center">{{ link_to_route('receipts.show', $receipt->number, [$receipt->number], ['target' => '_blank']) }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td class="text-center">{{ $receipt->destinationName() }}</td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
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
            $billAmountTotal += $receipt->bill_amount;
            ?>
            @empty
            <tr><td colspan="8">{{ trans('receipt.no_receipts') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="7">{{ trans('app.count') }}</th>
                <th class="text-right" colspan="1">{{ formatRp($billAmountTotal) }}</th>
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