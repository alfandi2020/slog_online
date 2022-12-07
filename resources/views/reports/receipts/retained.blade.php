@php
$date = Request::get('date', date('Y-m-d'));
@endphp
@extends('layouts.app')

@section('title', trans('report.receipt.retained'))

@section('content')

<div class="well well-sm">
    {{ Form::open(['route' => 'receipts.retained', 'method' => 'get', 'class' => 'form-inline pull-right']) }}
    {!! FormField::select('officer_id', $users, ['label' => 'Pilih Petugas', 'placeholder' => 'Semua']) !!}
    {{ Form::submit('Filter', ['class' => 'btn btn-info']) }}
    {{ link_to_route('receipts.retained', 'Reset', [], ['class' => 'btn btn-default']) }}
    {{ Form::close() }}
    <h3 style="margin:4px 0">{{ trans('report.receipt.retained') }} <small>Total {{ $receipts->count() }} {{ trans('receipt.receipt') }}</small></h3>
    <div class="clearfix"></div>
</div>

<p>Daftar Resi yang <b>tertahan</b> pada suatu cabang <b>lebih dari 2 hari</b>.</p>

<div class="panel panel-default table-responsive">
    <table class="table table-condensed table-hover">
        <thead>
            <th class="text-center">{{ trans('app.table_no') }}</th>
            <th class="text-center">{{ trans('receipt.number') }}</th>
            <th class="text-center">{{ trans('service.service') }}</th>
            <th class="">{{ trans('receipt.customer') }}</th>
            <th class="">{{ trans('receipt.destination') }}</th>
            <th class="text-center">{{ trans('app.status') }}</th>
            <th class="">{{ trans('receipt.location') }}</th>
            <th class="text-right">{{ trans('receipt.bill_amount') }}</th>
            <th>{{ trans('app.last_update') }}</th>
            <th>{{ trans('receipt.officer') }}</th>
        </thead>
        <tbody>
            @forelse($receipts as $key => $receipt)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">
                    {!! link_to_route('receipts.show', $receipt->number, [$receipt->number], ['title' => 'Lihat Detail ' . $receipt->number]) !!}
                </td>
                <td class="text-center">{{ strtoupper($receipt->service()) }}</td>
                <td>{{ $receipt->customer ? $receipt->customer->name : '' }}</td>
                <td>{{ $receipt->destinationName() }}</td>
                <td class="text-center">{!! $receipt->present()->statusLabel !!} {{-- <br>{{ $receipt->lastStatus() }} --}}</td>
                <td>{{ $receipt->lastLocation() }}</td>
                <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
                <td>{{ Date::parse($receipt->updated_at)->diffForHumans() }}</td>
                <td>{{ link_to_route('receipts.retained', $receipt->lastOfficer(), ['officer_id' => $receipt->lastOfficerId()]) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10">Tidak ada Resi Tertahan</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">{{ trans('app.total') }} : {{ formatRp($receipts->sum('bill_amount')) }}</th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</div>
{{-- {!! str_replace('/?', '?', $receipts->appends(Request::except('page'))->render()) !!} --}}

@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
@endpush

@section('script')
<script>
(function () {
    $('#officer_id').select2();
})();
</script>
@endsection