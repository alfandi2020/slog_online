<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('invoice.calculation') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><td class="col-xs-8">{{ trans('invoice.receipts_bill_amount') }}</td><td class="text-right">{{ formatRp($invoice->receipts->sum('bill_amount')) }}</td></tr>
            <tr><td>{{ trans('invoice.discount') }}</td><td class="text-right">{{ formatRp($invoice->charge_details['discount']) }}</td></tr>
            <tr class="operator-line operator-minus"><th>{{ trans('app.subtotal') }}</th><td class="text-right">{{ formatRp($subtotal = $invoice->receipts->sum('bill_amount') - $invoice->charge_details['discount']) }}</td></tr>
            <tr><td>{{ trans('invoice.admin_fee') }}</td><td class="text-right">{{ formatRp($invoice->charge_details['admin_fee']) }}</td></tr>
            @if ($invoice->customer->is_taxed)
                <tr class="operator-line operator-plus">
                    <th>{{ trans('app.subtotal') }}</th>
                    <td class="text-right">{{ formatRp($amount = $subtotal + $invoice->charge_details['admin_fee']) }}</td>
                </tr>
                <tr>
                    <td>{{ trans('invoice.ppn') }}</td>
                    @php
                        $popoverContent = '';
                        $popoverContent .= trans('invoice.base_tax');
                        $popoverContent .= ' = '.formatRp(0.011 * ($amount));
                        $popoverContent .= '<br>'.trans('invoice.ppn');
                        $popoverContent .= ' = 11% * '.trans('invoice.base_tax');
                        $popoverContent .= '<br>'.trans('invoice.ppn');
                        $popoverContent .= ' = 11% * '.formatRp(0.011 * ($amount));
                        $popoverContent .= ' = '.formatRp(0.011 * ($amount));
                    @endphp
                    <td
                        id="popoverData"
                        class="text-right"
                        style="vertical-align: bottom;cursor: pointer;"
                        data-html="true"
                        data-content="{{ $popoverContent }}"
                        rel="popover"
                        data-placement="bottom"
                        data-original-title="Kalkulasi PPN"
                        data-trigger="hover">
                        {{ formatRp(0.011 * ($amount)) }}
                    </td>
                </tr>
            @endif
            <tr class="operator-line operator-plus"><th>{{ trans('invoice.amount') }}</th><td class="text-right">{{ formatRp($invoice->getAmount()) }}</td></tr>
        </tbody>
    </table>
</div>
