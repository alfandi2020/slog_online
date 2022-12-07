@extends('layouts.app')

@if ($receipt->destination)
    @section('title')
    {!! trans('receipt.create_new_title', [
        'origin' => $receipt->originName(),
        'destination' => $receipt->destinationName()
    ]) !!} ({{ $receipt->type }})
    @endsection
@else
    @section('title', trans('receipt.create_new_from', [
            'origin' => $receipt->originName()
        ]))
@endif

@section('content')

    @include('receipts.partials.receipt-drafts-nav')
    <br>
    @if (in_array(request('step'), [null, 1]))

    @include('receipts.partials.form-cost-calculation')

    {{ Form::open(['route' => ['receipts.draft-update', $receipt->receiptKey], 'method' => 'patch']) }}
    <div class="row">
        <div class="col-sm-5">@include('receipts.partials.form-receipt-data')</div>
        <div class="col-sm-7">@include('receipts.partials.form-consignor-consignee')</div>
    </div>
    {{ Form::close() }}

    @endif

    @if (request('step') == 2)
    @include('receipts.partials.form-receipt-items')
    @endif

    @if (request('step') == 3)
    @include('receipts.partials.receipt-draft-review')
    @endif
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
{!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
{!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
@include('receipts.partials.receipt-draft-script')
@endsection