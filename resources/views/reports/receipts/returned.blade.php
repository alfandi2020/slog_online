@extends('layouts.app')

@section('title', trans('report.receipt.returned'))

@section('content')

{{ Form::open(['method' => 'get', 'class' => 'form-inline well well-sm text-right']) }}
<p class="pull-left" style="margin-top: 7px">Laporan Resi yang <strong>Kembali</strong> ke Kantor Cabang Pengirim tanggal <strong>{{ dateId($date) }}</strong>.</p>
{{ Form::label('date', __('app.date'), ['class' => 'control-label']) }}
{{ Form::text('date', $date, ['required', 'class' => 'form-control', 'style' => 'width:100px']) }}
{{ Form::submit(__('report.view_report'), ['class' => 'btn btn-info btn-sm']) }}
{{ link_to_route('reports.receipt.returned', __('report.view_today'), [], ['class' => 'btn btn-default btn-sm']) }}
{{ Form::close() }}

<div class="panel panel-default table-responsive">
    <table class="table table-condensed table-hover">
        <thead>
            <th>{{ trans('app.table_no') }}</th>
            <th class=" text-center">{{ trans('receipt.number') }}</th>
            <th class=" text-center">{{ trans('receipt.returned_time') }}</th>
            <th class="">{{ trans('receipt.officer') }}</th>
        </thead>
        <tbody>
            @forelse($receipts as $key => $receipt)
            <tr>
            <td>{{ $key + 1 }}</td>
                <td class="text-center">
                    {!! link_to_route('receipts.show', $receipt->number, [$receipt->number], ['title' => 'Lihat Detail ' . $receipt->number]) !!}
                </td>
                <td class="text-center">{{ $receipt->returned_time }}</td>
                <td>{{ $receipt->officer_name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">Belum ada resi kembali tanggal <strong>{{ dateId($date) }}</strong>.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('ext_css')
    {{ Html::style(url('css/plugins/jquery.datetimepicker.css')) }}
@endsection

@push('ext_js')
    {{ Html::script(url('js/plugins/jquery.datetimepicker.js')) }}
@endpush

@section('script')
<script>
(function() {
    $('#date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true,
        scrollInput: false
    });
})();
</script>
@endsection
