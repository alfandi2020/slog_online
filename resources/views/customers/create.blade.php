@extends('layouts.app')

@section('title', __('customer.create'))

@section('content')
<h2 class="page-header text-center">{{ __('customer.create') }}</h2>
{!! Form::open(['route' => 'customers.store']) !!}
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <legend>{{ __('customer.show') }}</legend>
                        {!! FormField::select('comodity_id', $comodities, [
                            'label' => __('comodity.comodity'),
                            'placeholder' => __('comodity.comodity'),
                            'required' => true,
                        ]) !!}
                        {!! FormField::select('network_id', $networks, [
                            'label' => __('network.network'),
                            'placeholder' => __('app.pick'),
                            'value' => Request::get('network_id'),
                            'required' => true,
                        ]) !!}
                        <div class="row">
                            <div class="col-sm-8">{!! FormField::text('name',['label' => __('app.name'), 'required' => true]) !!}</div>
                            <div class="col-sm-4">{!! FormField::text('code',['label' => __('app.code'), 'required' => true]) !!}</div>
                        </div>
                        {!! FormField::radios('category_id', config('bam-cargo.customer_categories'), ['label' => __('customer.category'), 'required' => true]) !!}
                    </div>
                    <div class="col-sm-4">
                        <legend>{{ __('customer.pic') }}</legend>
                        {!! FormField::text('pic[name]',['label' => __('app.name')]) !!}
                        <div class="row">
                            <div class="col-sm-6">{!! FormField::text('pic[phone]',['label' => __('contact.phone'), 'required' => true]) !!}</div>
                            <div class="col-sm-6">{!! FormField::email('pic[email]',['label' => __('contact.email'), 'required' => true]) !!}</div>
                        </div>
                        {!! FormField::text('address[1]',['label' => __('address.address'), 'required' => true]) !!}
                        {!! FormField::text('address[2]',['label' => false]) !!}
                        {!! FormField::text('address[3]',['label' => false]) !!}
                    </div>
                    <div class="col-sm-4">
                        <legend>{{ __('customer.show') }}</legend>
                        <div class="row">
                            <div class="col-sm-6">{!! FormField::text('start_date',['label' => __('customer.start_date'), 'class' => 'date-select', 'required' => true]) !!}</div>
                            <div class="col-sm-6">{!! FormField::radios('is_taxed', [__('app.no'), __('app.yes')],['label' => __('customer.is_taxed'), 'required' => true]) !!}</div>
                        </div>
                        {!! FormField::text('npwp',['label' => __('customer.npwp')]) !!}
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                {!! Form::submit(__('customer.create'), ['class' => 'btn btn-primary']) !!}
                {!! link_to_route('customers.index', __('app.cancel'), [], ['class' => 'btn btn-default']) !!}
            </div>
        </div>
    </div>
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
