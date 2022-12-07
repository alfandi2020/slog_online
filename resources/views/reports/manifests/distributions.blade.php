@extends('layouts.app')

@section('title', __('report.manifest.distributions'))

@section('content')
<div class="well well-sm">
    {{ Form::open(['method' => 'get', 'class' => 'form-inline pull-right']) }}
    {!! FormField::select('ym', $yearMonthList, ['label' => 'Tahun-Bulan', 'placeholder' => 'Semua', 'value' => $yearMonth]) !!}
    {{ Form::submit('Lihat Laporan', ['class' => 'btn btn-info']) }}
    {{ link_to_route('reports.manifests.distributions', __('app.reset'), [], ['class' => 'btn btn-default']) }}
    {{ Form::close() }}
    <h3 style="margin:4px 0">{{ __('report.manifest.distributions') }}</h3>
    <div class="clearfix"></div>
</div>

<div class="panel panel-default table-responsive">
<table class="table table-condensed">
    <thead>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('app.date') }}</th>
            <th>{{ trans('manifest.number') }}</th>
            <th>{{ trans('manifest.distributions.dest_city') }}</th>
            <th>{{ trans('manifest.courier') }}</th>
            <th class="text-center">{{ trans('manifest.receipts_count') }}</th>
            <th class="text-center">{{ trans('manifest.weight') }}</th>
            <th class="text-center">{{ trans('app.pc') }}</th>
            <th class="text-center">{{ trans('app.item') }}</th>
            <th class="text-right">{{ trans('receipt.base_charge') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($manifests as $key => $manifest)
        <tr>
            <td>{{ 1 + $key }}</td>
            <td class="text-center">{{ $manifest->date }}</td>
            <td>
                {{ link_to_route('manifests.distributions.show', $manifest->number, [$manifest->number], [
                    'title' => 'Lihat detail manifest ' . $manifest->number
                ]) }}
            </td>
            <td>{{ $manifest->destination }}</td>
            <td>{{ $manifest->courier }}</td>
            <td class="text-center">{{ $manifest->receipts_count }}</td>
            <td class="text-center">{{ $manifest->receipts_weight }}</td>
            <td class="text-center">{{ $manifest->receipts_pcs }}</td>
            <td class="text-center">{{ $manifest->receipts_items }}</td>
            <td class="text-right">{{ formatRp($manifest->receipts_bill_amount) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9">{{ trans('manifest.empty') }}</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th class="text-right" colspan="3">{{ __('app.total') }}</th>
            <th class="text-center">{{ $manifests->sum('receipts_count') }}</th>
            <th class="text-center">{{ $manifests->sum('receipts_weight') }}</th>
            <th class="text-center">{{ $manifests->sum('receipts_pcs') }}</th>
            <th class="text-center">{{ $manifests->sum('receipts_items') }}</th>
            <th class="text-right">{{ formatRp($manifests->sum('receipts_bill_amount')) }}</th>
        </tr>
        <tr>
            <th>{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('app.date') }}</th>
            <th>{{ trans('manifest.number') }}</th>
            <th class="text-center">{{ trans('manifest.receipts_count') }}</th>
            <th class="text-center">{{ trans('manifest.weight') }}</th>
            <th class="text-center">{{ trans('app.pc') }}</th>
            <th class="text-center">{{ trans('app.item') }}</th>
            <th class="text-right">{{ trans('receipt.base_charge') }}</th>
        </tr>
    </tfoot>
</table>
</div>
@endsection
