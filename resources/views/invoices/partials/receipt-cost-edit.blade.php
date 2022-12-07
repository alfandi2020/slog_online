<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    {!! trans('receipt.cost_edit_title', [
                        'number' => $editableReceipt->number,
                        'service' => $editableReceipt->service()
                    ]) !!}
                </h4>
            </div>
            {{ Form::open(['route' => ['invoices.receipt-cost-update', $editableReceipt->id], 'method' => 'patch']) }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="text-center">Paket</h4>
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td>{{ trans('receipt.weight') }}</td>
                                    <td class="text-right">{{ Form::text('weight', $editableReceipt->weight, ['class' => 'table-input text-right', 'style' => 'margin-right: 7px']) }} Kg</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.items_count') }}</td>
                                    <td class="text-right">{{ Form::text('items_count', $editableReceipt->items_count, ['class' => 'table-input text-right']) }} Dus</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.pcs_count') }}</td>
                                    <td class="text-right">{{ Form::text('pcs_count', $editableReceipt->pcs_count, ['class' => 'table-input text-right']) }} Koli</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.customer_invoice_no') }}</td>
                                    <td>{{ Form::text('customer_invoice_no', $editableReceipt->customer_invoice_no, ['class' => 'table-input', 'style' => 'width:100%']) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        {{ trans('receipt.notes') }}<br>
                                        {{ Form::textarea('notes', $editableReceipt->notes, ['class' => 'table-input', 'style' => 'width:100%', 'rows' => 5]) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul class="list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li>- {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <h4 class="text-center">Biaya</h4>
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td>{{ trans('receipt.base_rate') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('base_rate', $editableReceipt->base_rate, ['class' => 'table-input text-right']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.base_charge') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('base_charge', $editableReceipt->costs_detail['base_charge'], ['class' => 'table-input text-right']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.discount') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('discount', $editableReceipt->costs_detail['discount'], ['class' => 'table-input text-right']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.packing_cost') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('packing_cost', $editableReceipt->costs_detail['packing_cost'], ['class' => 'table-input text-right']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.insurance_cost') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('insurance_cost', $editableReceipt->costs_detail['insurance_cost'], ['class' => 'table-input text-right']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.add_cost') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('add_cost', $editableReceipt->costs_detail['add_cost'], ['class' => 'table-input text-right']) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('receipt.admin_fee') }}</td>
                                    <td class="text-right">Rp. {{ Form::text('admin_fee', $editableReceipt->costs_detail['admin_fee'], ['class' => 'table-input text-right']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit(trans('receipt.cost_detail_update'), ['class' => 'btn btn-success pull-left', 'name' => 'cost_detail_update']) }}
                @can ('recalculate-bill-amount', $editableReceipt)
                {{ Form::submit(trans('receipt.recalculate_bill_amount'), ['class' => 'btn btn-primary pull-right', 'name' => 'recalculate_bill_amount']) }}
                @endcan
                {{ link_to_route('invoices.edit', trans('app.cancel'), [$invoice->id], ['class' => 'btn btn-default']) }}
            </div>
            {{ Form::close() }}
        </div>

    </div>
</div>