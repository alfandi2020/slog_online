@inject('itemType', 'App\Entities\References\Reference')
@extends('layouts.receipt-detail')

@section('subtitle', trans('receipt.items'))

@section('show-links')
@can('print-items-label', $receipt)
{!! html_link_to_route('receipts.pdf-items-label', trans('receipt.pdf_items_label'), [$receipt->number], [
    'icon' => 'print',
    'class' => 'btn btn-info btn-sm',
    'target'=>'_blank',
]) !!}
@endcan
@endsection

@section('receipt-content')
@if ($receipt->items_detail)
<div class="panel panel-default table-responsive">
    <table class="table">
        <thead>
            <th class="text-center col-md-1">{{ trans('receipt.item_no') }}</th>
            <th class="text-center col-md-1">{{ trans('receipt.weight') }}</th>
            <th class="text-center col-md-1">{{ trans('receipt.length') }}</th>
            <th class="text-center col-md-1">{{ trans('receipt.width') }}</th>
            <th class="text-center col-md-1">{{ trans('receipt.height') }}</th>
            <th class="text-center col-md-2">{{ trans('receipt.volume') }}</th>
            <th class="text-center col-md-1">{{ trans('receipt.volumetric_weight') }}</th>
            <th class="text-right col-md-1">{{ trans('receipt.weight') }}</th>
            <th class="text-center col-md-1">{{ trans('receipt.pack_type') }}</th>
            <th class="col-md-2">{{ trans('app.notes') }}</th>
        </thead>
        <tbody>
            <?php
                $no = 1;
                $itemsCount = 0;
                $chargedWeightSum = 0;
            ?>
            @foreach($receipt->items_detail as $key => $item)
            <tr>
                <td class="text-center"><?php echo str_pad($no, 3, "0", STR_PAD_LEFT); $no++ ?></td>
                <td class="text-center">{{ $item['weight'] }} Kg</td>
                <td class="text-center">{{ $item['length'] }} cm</td>
                <td class="text-center">{{ $item['width'] }} cm</td>
                <td class="text-center">{{ $item['height'] }} cm</td>
                <td class="text-right">{{ formatDecimal($item['volume']) }} cm3</td>
                <td class="text-right">{{ formatDecimal($item['volumetric_weight']) }} Kg</td>
                <td class="text-right">{{ formatDecimal($item['charged_weight']) }} Kg</td>
                <td class="text-center">{{ $itemType::findOrFail($item['type_id'])->name }}</td>
                <td>{{ $item['notes'] }}</td>
            </tr>
            <?php
            $chargedWeightSum += $item['charged_weight'];
            ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="text-center" colspan="3">
                    {{ trans('receipt.volumetric_devider') }}
                    <p class="lead text-success">{{ $receipt->volumetric_devider }}</p>
                </td>
                <td class="text-center" colspan="2">
                    Total Jml <p class="lead text-success">{{ count($receipt->items_detail) }} Koli</p>
                </td>
                <td class="text-center" colspan="2">
                    Total Berat <p class="lead text-success">{{ formatDecimal($chargedWeightSum) }} Kg</p>
                </td>
                <td class="text-center" colspan="2">
                    Total Berat Pembulatan <p class="lead text-success">{{ ceil($chargedWeightSum) }} Kg</p>
                </td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    </table>
</div>
@else
<div class="alert alert-info">Tidak ada Rincian Item untuk Resi ini.</div>
@endif
@endsection