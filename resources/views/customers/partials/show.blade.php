<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('customer.detail') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th>{{ trans('customer.account_no') }}</th><td>{{ $customer->account_no }}</td></tr>
            <tr><th>{{ trans('customer.code') }}</th><td>{{ $customer->code }}</td></tr>
            <tr><th>{{ trans('customer.name') }}</th><td>{{ $customer->name }}</td></tr>
            <tr><th>{{ trans('comodity.comodity') }}</th><td>{{ $customer->comodity->name }}</td></tr>
            <tr><th>{{ trans('network.network') }}</th><td>{{ $customer->network->name }}</td></tr>
            <tr><th>{{ trans('customer.pic') }}</th><td>{!! implode('<br>', $customer->pic) !!}</td></tr>
            <tr><th>{{ trans('address.address') }}</th><td>{!! implode('<br>', $customer->address) !!}</td></tr>
            <tr><th>{{ trans('customer.start_date') }}</th><td>{{ $customer->start_date }}</td></tr>
            <tr><th>{{ trans('customer.is_taxed') }}</th><td>{{ $customer->isTaxed() }}</td></tr>
            <tr><th>{{ trans('customer.npwp') }}</th><td>{{ $customer->npwp }}</td></tr>
        </tbody>
    </table>
    </div>
