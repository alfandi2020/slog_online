@extends('layouts.app')

@section('title', trans('receipt.create_new_from', ['origin' => $receipt->originName()]) . ' (' . $receipt->type . ')')

@section('content')

    @include('receipts.partials.receipt-drafts-nav')
    <br>
    @include('receipts.drafts.partials.project-receipt-draft-step-nav')
    @if (in_array(request('step'), [null, 1]))
       @include('receipts.forms.form-project-receipt-items')
    @endif

    @if (request('step') == 2)
        {{ Form::open(['route' => ['receipts.draft-project-update', $receipt->receiptKey], 'method' => 'patch']) }}
        @include('receipts.drafts.forms.form-project-receipt-data')
        <div class="row">
            <div class="col-sm-5">@include('receipts.drafts.forms.form-project-cost-entry')</div>
            <div class="col-sm-7">@include('receipts.partials.form-consignor-consignee', ['submitValue' => trans('receipt.submit_and_review')])</div>
        </div>
        {{ Form::close() }}
    @endif

    @if (request('step') == 3)
        @include('receipts.drafts.forms.project-receipt-draft-review')
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