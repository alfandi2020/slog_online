<div class="panel panel-default table-responsive hidden-xs">
    <table class="table table-condensed table-bordered">
        <tr>
            <td class="col-xs-2 text-center">{{ trans('receipt.number') }}</td>
            <td class="col-xs-2 text-center">{{ trans('receipt.status') }}</td>
            <td class="col-xs-2 text-center">{{ trans('receipt.payment_type') }}</td>
            <td class="col-xs-2 text-center">{{ trans('receipt.payment_status') }}</td>
            <td class="col-xs-2 text-center">{{ trans('receipt.location') }}</td>
            <td class="col-xs-2 text-center">{{ trans('receipt.handler') }}</td>
        </tr>
        <tr>
            <td class="text-center lead" style="border-top: none;">{{ $receipt->number }}</td>
            <td class="text-center lead" style="border-top: none;">{!! $receipt->present()->statusLabel !!}</td>
            <td class="text-center lead" style="border-top: none;">{!! $receipt->present()->paymentType !!}</td>
            <td class="text-center lead" style="border-top: none;">{!! $receipt->present()->paymentStatusLabel !!}</td>
            <td class="text-center lead" style="border-top: none;">{!! $receipt->lastLocation() !!}</td>
            <td class="text-center lead" style="border-top: none;">{!! $receipt->lastOfficer() !!}</td>
        </tr>
    </table>
</div>

<ul class="list-group visible-xs">
    <li class="list-group-item">{{ trans('receipt.number') }} <span class="pull-right">{{ $receipt->number }}</span></li>
    <li class="list-group-item">{{ trans('receipt.status') }} <span class="pull-right">{!! $receipt->present()->statusLabel !!}</span></li>
    <li class="list-group-item">{{ trans('receipt.payment_type') }} <span class="pull-right">{!! $receipt->present()->paymentType !!}</span></li>
    <li class="list-group-item">{{ trans('receipt.payment_status') }} <span class="pull-right">{!! $receipt->present()->paymentStatusLabel !!}</span></li>
    <li class="list-group-item">{{ trans('receipt.location') }} <span class="pull-right">{!! $receipt->lastLocation() !!}</span></li>
    <li class="list-group-item">{{ trans('receipt.handler') }} <span class="pull-right">{!! $receipt->lastOfficer() !!}</span></li>
</ul>