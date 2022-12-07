<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.detail') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th class="col-xs-6">{{ trans('pickup.number') }}</th><td class="col-xs-6">{{ $pickup->number }}</td></tr>
            <tr><th>{{ trans('pickup.courier') }}</th><td>{{ $pickup->courier->name }}</td></tr>
            <tr><th>{{ trans('pickup.customers_count') }}</th><td>{{ $pickup->customers_count }}</td></tr>
            <tr><th>{{ trans('pickup.receipts_count') }}</th><td>{{ $pickup->receipts_count }}</td></tr>
            <tr><th>{{ trans('pickup.pcs_count') }}</th><td>{{ $pickup->pcs_count }}</td></tr>
            <tr><th>{{ trans('app.status') }}</th><td>{!! $pickup->status_label !!}</td></tr>
            <tr><th>{{ trans('app.created_by') }}</th><td>{{ $pickup->creator->name }}</td></tr>
            <tr><th>{{ trans('network.network') }}</th><td>{{ $pickup->network->name }}</td></tr>
            <tr><th>{{ trans('app.created_at') }}</th><td>{{ $pickup->created_at->format('Y-m-d H:i') }}</td></tr>
            <tr><th>{{ trans('app.updated_at') }}</th><td>{{ $pickup->updated_at->format('Y-m-d H:i') }}</td></tr>
        </tbody>
    </table>
</div>
