@extends('layouts.receipt-detail')

@section('show-links')
@can('edit', $receipt)
{!! html_link_to_route('receipts.edit', trans('receipt.edit'), [$receipt->number], [
    'icon' => 'edit', 'class' => 'btn btn-warning btn-sm',
]) !!}
@endcan
{{-- {!! html_link_to_route('receipts.pdf', trans('receipt.pdf'), [$receipt->number], [
    'icon' => 'print', 'class' => 'btn btn-info btn-sm', 'target'=>'_blank',
]) !!} --}}
{!! html_link_to_route('receipts.pdf_v2', trans('receipt.pdf'), [$receipt->number], [
    'icon' => 'print', 'class' => 'btn btn-info btn-sm', 'target'=>'_blank',
]) !!}

@endsection

@section('receipt-content')
<div class="row">
    <div class="col-sm-4">
        @include('receipts.partials.receipt-data')
        @include('receipts.partials.receipt-customer')
    </div>
    <div class="col-sm-4">
        @include('receipts.partials.receipt-package')
        @include('receipts.partials.receipt-costs-detail')
    </div>
    <div class="col-sm-4">
        @include('receipts.partials.receipt-consignor')
        @include('receipts.partials.receipt-consignee')
    </div>
</div>
@endsection
