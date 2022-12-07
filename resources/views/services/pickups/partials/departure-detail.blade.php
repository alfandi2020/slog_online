<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.departure_detail') }}</h3></div>
    <table class="table table-condensed">
        <tbody>
            <tr><th>{{ trans('pickup.sent_at') }}</th><td>{{ optional($pickup->sent_at)->format('Y-m-d H:i') }}</td></tr>
            <tr><th>{{ trans('pickup.start_km') }}</th><td class="text-right">{{ formatNo($pickup->start_km) }}</td></tr>
            <tr><th>{{ trans('pickup.returned_at') }}</th><td>{{ optional($pickup->returned_at)->format('Y-m-d H:i') }}</td></tr>
            <tr><th>{{ trans('pickup.end_km') }}</th><td class="text-right">{{ formatNo($pickup->end_km) }}</td></tr>
        </tbody>
    </table>
</div>
