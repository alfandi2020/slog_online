<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.costs_detail') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            @if ($receipt->isDraft())
            <tr><th>{{ trans('receipt.costs') }}</th><td class="text-right">{{ formatRp($receipt->base_charge) }}</td></tr>
            <tr><th>{{ trans('receipt.discount') }}</th><td class="text-right">{{ formatRp($receipt->discount) }}</td></tr>
            <tr><th>{{ trans('receipt.subtotal') }}</th><td class="text-right">{{ formatRp($receipt->subtotal) }}</td></tr>
            <tr><th>{{ trans('receipt.packing_cost') }}</th><td class="text-right">{{ formatRp($receipt->packing_cost) }}</td></tr>
            <tr><th>{{ trans('receipt.add_cost') }}</th><td class="text-right">{{ formatRp($receipt->add_cost) }}</td></tr>
            <tr><th>{{ trans('receipt.insurance_cost') }}</th><td class="text-right">{{ formatRp($receipt->insurance_cost) }}</td></tr>
            <tr><th>{{ trans('receipt.admin_fee') }}</th><td class="text-right">{{ formatRp($receipt->admin_fee) }}</td></tr>
            <tr><th>{{ trans('receipt.total') }}</th><td class="text-right"><strong>{{ formatRp($receipt->total) }}</strong></td></tr>
            @else
            <tr><th>{{ trans('receipt.base_charge') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['base_charge']) }}</td></tr>
            <tr><th>{{ trans('receipt.discount') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['discount']) }}</td></tr>
            <tr><th>{{ trans('receipt.subtotal') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['subtotal']) }}</td></tr>
            <tr><th>{{ trans('receipt.packing_cost') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['packing_cost']) }}</td></tr>
            <tr><th>{{ trans('receipt.insurance_cost') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['insurance_cost']) }}</td></tr>
            <tr><th>{{ trans('receipt.add_cost') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['add_cost']) }}</td></tr>
            <tr><th>{{ trans('receipt.admin_fee') }}</th><td class="text-right">{{ formatRp($receipt->costs_detail['admin_fee']) }}</td></tr>
            <tr><th>{{ trans('receipt.total') }}</th><td class="text-right"><strong>{{ formatRp($receipt->bill_amount) }}</strong></td></tr>
            @endif
        </tbody>
    </table>
</div>
