<div class="panel panel-{{ $manifest->present()->status['class'] }}">
    <div class="panel-heading">
        <span class="pull-right strong">{{ $manifest->present()->status['name'] }}</span>
        <h3 class="panel-title">{{ trans('manifest.manifest') }}</h3>
    </div>
    <br>
    <table class="table table-condensed">
        <tr><th>{{ trans('manifest.number') }}</th><td>{{ $manifest->number }}</td></tr>
        <tr><th>{{ trans('app.date') }}</th><td>{{ $manifest->created_at->format('Y-m-d') }}</td></tr>
        <tr><th>{{ trans('manifest.type') }}</th><td>{!! $manifest->present()->typeLabel !!}</td></tr>
        <tr><th>{{ trans('manifest.receipts_count') }}</th><td>{{ $manifest->receipts->count() }}</td></tr>
        <tr><th>{{ trans('manifest.creator') }}</th><td>{{ $manifest->present()->creatorName }}</td></tr>
        <tr><th>{{ trans('manifest.handler') }}</th><td>{{ $manifest->present()->handlerName }}</td></tr>
        <tr><th>{{ trans('manifest.accountings.customer') }}</th><td>{{ $manifest->present()->customerLink() }}</td></tr>
        <tr>
            <th>{{ trans('app.status') }}</th>
            <td>{!! $manifest->present()->statusLabel !!}</td>
        </tr>
        <tr><th>{{ trans('app.notes') }}</th><td>{{ $manifest->notes }}</td></tr>
    </table>
</div>