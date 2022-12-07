@extends('layouts.manifest-detail')

@section('show-links')
    {!! html_link_to_route('manifests.accountings.excel', trans('app.export_excel'), $manifest->number, ['class' => 'btn btn-default','icon' => 'download']) !!}
    @include('manifests.partials.show-links')
@endsection

@section('manifest-content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data-accounting')
    </div>
    <div class="col-md-8">
        @can ('add-remove-receipt-of', $manifest)
        <div class="alert alert-info">{{ trans('manifest.add_receipt_instruction') }}</div>
        @include('manifests.partials.form-add-remove-receipts', [
            'assignRoute' => 'manifests.accountings.assign-receipt',
            'removeRoute' => 'manifests.remove-receipt',
            'manifestId' => $manifest->id,
        ])
        @endcan

        <div class="panel panel-{{ $manifest->present()->status['class'] }} table-responsive">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('manifest.receipt_lists') }}</h3></div>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th class="col-md-2 text-center">{{ trans('receipt.number') }}</th>
                        <th class="col-md-6">{{ trans('receipt.consignor') }} / {{ trans('receipt.consignee') }}</th>
                        <th class="col-md-2 text-right">{{ trans('receipt.bill_amount') }}</th>
                        <th class="col-md-2 text-center">{{ trans('receipt.weight') }} / {{ trans('receipt.items_count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $billTotal = 0; ?>
                    @forelse($manifest->receipts as $key => $receipt)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-center">
                            {{ link_to_route('receipts.show', $receipt->number, [$receipt->number], [
                                'title' => 'Lihat detail Resi ' . $receipt->number,
                            ]) }}<br>
                            <span class="badge">{{ $receipt->packType->name }}</span>
                            @if ($receipt->pivot->handler_id && $receipt->pivot->end_status == 'no')
                                <span class="label label-danger">Not OK</span>
                            @elseif ($receipt->pivot->handler_id && $receipt->pivot->end_status != 'no')
                                <span class="label label-success">OK</span>
                            @endif
                            <div>{{ strtoupper($receipt->service()) }}</div>
                        </td>
                        <td>
                            <p><strong>Dari :</strong> {{ $receipt->consignor['name'] }} ({{ $receipt->originName() }})</p>
                            <p><strong>Kepada:</strong> {{ $receipt->consignee['name'] }} ({{ $receipt->destinationName() }})</p>
                        </td>
                        <td class="text-right">
                            <div>{{ formatRp($receipt->bill_amount) }}</div>
                            <div>({{ $receipt->payment_type }})</div>
                        </td>
                        <td class="text-center">
                            <span
                                style="cursor: pointer"
                                title="{{ trans('receipt.weight') }} : {{ $receipt->weight }} Kg">
                                {{ $receipt->weight }}
                            </span> /
                            <span
                                style="cursor: pointer"
                                title="{{ $receipt->items_count }} {{ trans('receipt.items_count') }}">
                                {{ $receipt->items_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8">{{ trans('receipt.empty') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">
                            {{ trans('app.total') }} :
                            {{ formatRp($manifest->receipts->sum('bill_amount')) }}
                        </th>
                        <th class="text-center">
                            <span
                                style="cursor: pointer"
                                title="{{ trans('receipt.weight') }} : {{ $manifest->receipts->sum('weight') }} Kg">
                            {{ formatDecimal($manifest->receipts->sum('weight')) }}
                            </span> /
                            <span
                                style="cursor: pointer"
                                title="{{ $manifest->receipts->sum('items_count') }} {{ trans('receipt.items_count') }}">
                                {{ $manifest->receipts->sum('items_count') }}
                            </span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection