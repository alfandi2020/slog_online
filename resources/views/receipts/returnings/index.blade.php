@extends('layouts.app')

@section('title', __('receipt.returnings_entry'))

@section('content')
@include('manifests.partials.form-add-remove-receipts', [
    'assignRoute' => 'receipts.returnings.store',
    'removeRoute' => 'receipts.returnings.remove',
    'manifestId' => null,
])

@if (empty($returningReceipts))
<div class="alert alert-info">
    {{ __('receipt.search_alert_info') }}
</div>
@else
<div class="panel panel-default table-responsive">
    <div class="panel-heading">
        {!! FormField::delete(
            ['route' => 'receipts.returnings.destroy', 'onsubmit' => 'Yakin ingin mengosongkan List Resi Kembali?'],
            __('receipt.returnings_destroy'),
            ['class' => 'btn btn-danger btn-xs', 'id' => 'destroy-returning-list']
        ) !!}
        <h3 class="panel-title">{{ __('receipt.returnings') }}</h3>
    </div>
    <table class="table table-condensed table-hover">
        <thead>
            <th>{{ __('app.table_no') }}</th>
            <th class=" text-center">{{ __('receipt.number') }}</th>
            <th class="">{{ __('receipt.consignor') }}</th>
            <th class="">{{ __('receipt.consignee') }}</th>
            <th class=" text-center">{{ __('receipt.destination') }}</th>
            <th class="text-center">{{ __('service.service') }}</th>
            <th class="text-center">Koli</th>
            <th class="text-center">{{ __('receipt.weight') }}</th>
            <th class="">{{ __('app.status') }}</th>
        </thead>
        <tbody>
            @forelse($returningReceipts as $key => $receipt)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="text-center">
                    {!! link_to_route('receipts.show', $receipt->number, [$receipt->number], ['title' => 'Lihat Detail ' . $receipt->number]) !!}
                </td>
                <td>{{ $receipt->consignor['name'] }}</td>
                <td>{{ $receipt->consignee['name'] }}</td>
                <td class="text-center">{{ $receipt->destinationName() }}</td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td class="text-center">{{ $receipt->items_count }}</td>
                <td class="text-center">{{ $receipt->weight }}</td>
                <td>{!! $receipt->present()->statusLabel !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11">{{ __('receipt.not_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="panel-body">
        {{ Form::open([
            'route' => 'receipts.returnings.set-returned',
            'method' => 'patch', 'class' => 'form-inline',
            'onsubmit' => 'return confirm("Anda yakin akan set semua resi telah kembali?")',
        ]) }}
        {!! FormField::text('returned_time', ['value' => date('Y-m-d H:i'),'label' => __('receipt.returned_time')]) !!}
        {{ Form::submit(__('receipt.set_all_returned'), ['class' => 'btn btn-success']) }}
        {{ Form::close() }}
        <br>
        <p class="text-danger"><strong>Mohon dipastikan</strong> bahwa semua resi <strong>telah lengkap</strong>, karena setelah ber-status <strong>Return</strong>, status <strong>tidak bisa dikembalikan</strong>.</p>
    </div>
</div>

@endif

@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#returned_time').datetimepicker({
        format:'Y-m-d H:i'
    });
})();
</script>
@endsection
