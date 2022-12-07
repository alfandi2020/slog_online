<div class="panel panel-default table-responsive hidden-xs">
    <table class="table table-condensed table-bordered">
        <tr>
            <td class="col-xs-2 text-center">{{ trans('manifest.number') }}</td>
            <td class="col-xs-2 text-center">{{ trans('app.status') }}</td>
            <td class="col-xs-2 text-center">{{ trans('manifest.receipts_count') }}</td>
            <td class="col-xs-2 text-center">{{ trans('manifest.bill_total') }}</td>
            <td class="col-xs-2 text-center">{{ trans('manifest.created_by') }}</td>
            <td class="col-xs-2 text-center">{{ trans('manifest.handler') }}</td>
        </tr>
        <tr>
            <td class="text-center lead" style="border-top: none;">{{ $manifest->number }}</td>
            <td class="text-center lead" style="border-top: none;">{!! $manifest->present()->statusLabel !!}</td>
            <td class="text-center lead" style="border-top: none;">{!! $manifest->receipts->count() !!}</td>
            <td class="text-center lead" style="border-top: none;">{{ formatRp($manifest->receipts->sum('bill_amount')) }}</td>
            <td class="text-center lead" style="border-top: none;">{{ $manifest->present()->creatorName }}</td>
            <td class="text-center lead" style="border-top: none;">{{ $manifest->present()->handlerName }}</td>
        </tr>
    </table>
</div>

<ul class="list-group visible-xs">
    <li class="list-group-item">{{ trans('manifest.number') }} <span class="pull-right">{{ $manifest->number }}</span></li>
    <li class="list-group-item">{{ trans('manifest.status') }} <span class="pull-right">{!! $manifest->present()->statusLabel !!}</span></li>
    <li class="list-group-item">{{ trans('manifest.receipts_count') }} <span class="pull-right">{{ $manifest->receipts->count() }}</span></li>
    <li class="list-group-item">{{ trans('manifest.bill_total') }} <span class="pull-right">{{ formatRp($manifest->receipts->sum('bill_amount')) }}</li>
    <li class="list-group-item">{{ trans('manifest.created_by') }} <span class="pull-right">{{ $manifest->present()->creatorName }}</li>
    <li class="list-group-item">{{ trans('manifest.handler') }} <span class="pull-right">{{ $manifest->present()->handlerName }}</span></li>
</ul>