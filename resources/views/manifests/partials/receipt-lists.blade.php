{{-- Receipt List Table --}}
<div class="panel panel-{{ $class ?? 'default' }} table-responsive">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('manifest.receipt_lists') }}</h3></div>
    <table class="table table-condensed">
        <thead>
            <tr>
                <th class="col-md-1 text-center">{{ trans('app.table_no') }}</th>
                <th class="col-md-2">{{ trans('receipt.number') }}</th>
                <th class="col-md-5">{{ trans('receipt.consignor') }} / {{ trans('receipt.consignee') }}</th>
                <th class="col-md-3 text-center">{{ trans('receipt.weight') }} / {{ trans('receipt.pcs_count') }} / {{ trans('receipt.items_count') }}</th>
                <th class="col-md-1 text-center">{{ trans('service.service') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $key => $receipt)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="show-on-hover-parent">
                    {{ $receipt->numberLink() }}<br>
                    <span class="badge">{{ $receipt->packType->name }}</span>
                    @if ($receipt->pivot->handler_id && $receipt->pivot->end_status == 'no')
                        <span class="label label-danger">Not OK</span>
                    @elseif ($receipt->pivot->handler_id && $receipt->pivot->end_status != $receipt->status_code)
                    @elseif ($receipt->pivot->handler_id && $receipt->pivot->end_status != 'no')
                        <span class="label label-success">OK</span>
                    @endif
                    {{-- {!! html_link_to_route('receipts.show', '', [$receipt->number], [
                        'icon' => 'search',
                        'class' => 'pull-right show-on-hover',
                        'title' => 'Lihat detail Resi ' . $receipt->number,
                    ]) !!} --}}
                </td>
                <td>
                    {{-- Commented for troubleshooting --}}
                    {{-- <div>{{ App\Entities\Receipts\Status::getNameById($receipt->status_code) }}</div>
                    <div>{{ $receipt->lastStatus() }}</div>
                    <br> --}}

                    <p><strong>Dari :</strong> {{ $receipt->consignor['name'] }} ({{ $receipt->originName() }})</p>
                    <p><strong>Kepada:</strong> {{ $receipt->consignee['name'] }} ({{ $receipt->destinationName() }})</p>
                </td>
                <td class="text-center">{{ $receipt->weight }} / {{ $receipt->pcs_count }} / {{ $receipt->items_count }}</td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
            </tr>
            @empty
            <tr><td colspan="8">{{ trans('receipt.empty') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
