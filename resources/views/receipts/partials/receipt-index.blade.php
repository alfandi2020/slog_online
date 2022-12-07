<div class="panel panel-default table-responsive">
<table class="table table-condensed table-hover">
    <thead>
        <th>{{ trans('app.table_no') }}</th>
        <th class=" text-center">{{ trans('receipt.number') }}</th>
        <th class=" text-center">{{ trans('app.time') }}</th>
        <th class="">{{ trans('receipt.consignor') }}</th>
        <th class="">{{ trans('receipt.consignee') }}</th>
        <th class="text-center">{{ trans('receipt.origin') }}</th>
        <th class=" text-center">{{ trans('receipt.destination') }}</th>
        <th class="text-center">{{ trans('service.service') }}</th>
        <th class="text-center">Koli</th>
        <th class="text-center">{{ trans('receipt.weight') }}</th>
        <th class="">{{ trans('app.status') }}</th>
        <th style="width:105px">{{ trans('app.action') }}</th>
    </thead>
    <tbody>
        @forelse($receipts as $key => $receipt)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td class="text-center">
                {!! link_to_route('receipts.show', $receipt->number, [$receipt->number], ['title' => 'Lihat Detail ' . $receipt->number]) !!}
            </td>
            <td class="text-center">{{ $receipt->pickup_time->format('H:i') }}</td>
            <td>{{ $receipt->consignor['name'] }}</td>
            <td>{{ $receipt->consignee['name'] }}</td>
            <td class="text-center">{{ $receipt->originName() }}</td>
            <td class="text-center">{{ $receipt->destinationName() }}</td>
            <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
            <td class="text-center">{{ $receipt->items_count }}</td>
            <td class="text-center">{{ $receipt->weight }}</td>
            <td>{!! $receipt->present()->statusLabel !!}</td>
            <td>
                {{-- {!! link_to_route('receipts.show',trans('app.show'),[$receipt->number] + urlParams(),['class'=>'btn btn-success btn-xs','title'=>'Lihat Detail ' . $receipt->number]) !!} --}}
                {!! html_link_to_route('receipts.show', '', [$receipt->number], [
                    'icon' => 'search',
                    'class' => 'btn btn-info btn-xs',
                    'title' => 'Lihat Detail ' . $receipt->number,
                ]) !!}
                {!! html_link_to_route('receipts.pdf', '', [$receipt->number, 3],[
                    'icon' => 'print',
                    'class' => 'btn btn-success btn-xs',
                    'title' => 'Cetak ' . $receipt->number,'target'=>'_blank'
                ]) !!}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11">{{ trans('receipt.not_found') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
