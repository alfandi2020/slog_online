@extends('layouts.receipt-detail')
@section('subtitle', trans('receipt.delete'))
@section('receipt-content')
<div class="row">
    <div class="col-sm-4">
        @include('receipts.partials.receipt-data')
        @include('receipts.partials.receipt-customer')
        @include('receipts.partials.receipt-consignor')
    </div>
    <div class="col-sm-4">
        @include('receipts.partials.receipt-package')
        @include('receipts.partials.receipt-costs-detail')
        @include('receipts.partials.receipt-consignee')
    </div>
    <div class="col-sm-4">
        <legend>{{ trans('receipt.delete') }}</legend>
        {{ Form::open([
            'route' => ['receipts.destroy', $receipt->number],
            'method' => 'delete',
            'onsubmit' => 'return confirm("' . trans('app.delete_confirm') . '")'
        ]) }}
        {!! FormField::textarea('notes', [
            'label' => 'Tuliskan alasan menghapus Resi',
            'placeholder' => trans('receipt.delete_reason'),
            'required' => true,
            'value' => $receipt->notes,
        ]) !!}
        {{ Form::submit(trans('receipt.delete'), ['class' => 'btn btn-danger']) }}
        {{ link_to_route('receipts.edit', trans('app.cancel'), [$receipt->number], ['class' => 'btn btn-default pull-right']) }}
        {{ Form::close() }}
    </div>
</div>
@endsection