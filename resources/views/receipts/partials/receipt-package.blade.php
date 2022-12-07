<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.package') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th>{{ trans('receipt.pcs_count') }}</th><td>{{ $receipt->pcs_count }}</td></tr>
            <tr><th>{{ trans('receipt.items_count') }}</th><td>{{ $receipt->items_count }}</td></tr>
            <tr><th>{{ trans('receipt.charged_weight') }}</th><td>{{ displayWeight($receipt->charged_weight ?: $receipt->weight) }}</td></tr>
            <tr><th>{{ trans('receipt.pack_type') }}</th><td>{{ $receipt->packType ? $receipt->packType->name : '' }}</td></tr>
            <tr><th>{{ trans('receipt.pack_content') }}</th><td>{{ $receipt->pack_content }}</td></tr>
        </tbody>
    </table>
</div>
