@extends('layouts.app')

@section('title', $customer->present()->numberName.' | '.__('customer.edit'))

@section('content')
{!! Form::model($customer, ['route' => ['customers.update', $customer], 'method' => 'patch']) !!}
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ __('customer.edit') }}</h3></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        {!! FormField::textDisplay(__('customer.account_no'), $customer->account_no) !!}
                    </div>
                    <div class="col-sm-8">
                        {!! FormField::textDisplay(__('network.network'), $customer->network->name) !!}
                    </div>
                </div>
                {!! FormField::select('comodity_id', $comodities, [
                    'label' => __('comodity.comodity'),
                    'placeholder' => __('comodity.comodity')
                ]) !!}
                <div class="row">
                    <div class="col-sm-8">{!! FormField::text('name', ['label' => __('app.name')]) !!}</div>
                    <div class="col-sm-4">{!! FormField::text('code', ['label' => __('app.code')]) !!}</div>
                </div>
                {!! FormField::radios('category_id', config('bam-cargo.customer_categories'), ['label' => __('customer.category'), 'required' => true]) !!}
                {!! FormField::text('address[1]',['label'=> trans('address.address')]) !!}
                {!! FormField::text('address[2]',['label'=> false]) !!}
                {!! FormField::text('address[3]',['label'=> false]) !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ __('customer.pic') }}</h3></div>
            <div class="panel-body">
                {!! FormField::text('pic[name]',['label'=> trans('app.name_opr')]) !!}
                <div class="row">
                    <div class="col-sm-5">{!! FormField::text('pic[phone]', ['label' => __('contact.phone')]) !!}</div>
                    <div class="col-sm-7">{!! FormField::email('pic[email]', ['label' => __('contact.email')]) !!}</div>
                </div>
                <hr style="margin: 10px 0 10px 0">
                {!! FormField::text('pic[name_acc]',['label'=> trans('app.name_acc')]) !!}
                <div class="row">
                    <div class="col-sm-5">{!! FormField::text('pic[phone_acc]',['label'=> trans('contact.phone')]) !!}</div>
                    <div class="col-sm-7">{!! FormField::email('pic[email_acc]',['label'=> trans('contact.email')]) !!}</div>
                </div>
                <hr style="margin: 10px 0 10px 0">
                {!! FormField::text('pic[name_prc]',['label'=> trans('app.name_prc')]) !!}
                <div class="row">
                    <div class="col-sm-5">{!! FormField::text('pic[phone_prc]',['label'=> trans('contact.phone')]) !!}</div>
                    <div class="col-sm-7">{!! FormField::email('pic[email_prc]',['label'=> trans('contact.email')]) !!}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ __('customer.show') }}</h3></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">{!! FormField::text('start_date', ['label' => __('customer.start_date'), 'class' => 'date-select']) !!}</div>
                    <div class="col-sm-6">{!! FormField::radios('is_taxed', [__('app.no'), __('app.yes')], ['label' => __('customer.is_taxed')]) !!}</div>
                </div>
                {!! FormField::text('npwp', ['label' => __('customer.npwp')]) !!}
                {!! FormField::radios('is_active', [__('app.no'), __('app.yes')], [
                    'label' => __('customer.is_active'),
                    'info' => [
                        'text' => 'Jika customer <strong>tidak aktif</strong>, maka tidak dapat <strong>Entry Resi</strong> untuk customer tersebut.',
                        'class' => 'text-danger small',
                    ],
                ]) !!}
            </div>
        </div>
    </div>
</div>

<div class="panel-footer">
    {!! Form::submit(__('customer.update'), ['class' =>'btn btn-primary']) !!}
    {!! link_to_route('customers.show', __('app.back_to').' '.__('customer.show'), $customer, ['class' => 'btn btn-default']) !!}
    {!! link_to_route('customers.delete', __('customer.delete'), $customer, ['class' =>'btn btn-danger pull-right']) !!}
</div>
{!! Form::close() !!}
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
    $('.date-select').datetimepicker({
        timepicker: false,
        format:'Y-m-d',
        closeOnDateSelect: true
    });
})();
</script>
@endsection
