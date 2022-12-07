@extends('layouts.app')

@section('title', trans('invoice.delete'))

@section('content')
<h2 class="page-header">
    <div class="pull-right">
        {!! FormField::delete(['route'=>['invoices.destroy',$invoice->id]], trans('invoice.delete'), ['class'=>'btn btn-danger'], ['invoice_id'=>$invoice->id]) !!}
    </div>
    {{ trans('app.delete_confirm') }}
    {!! link_to_route('invoices.edit', trans('app.cancel'), [$invoice->id], ['class' => 'btn btn-default']) !!}
</h2>
<div class="row">
    <div class="col-md-4">@include('invoices.partials.detail')</div>
</div>
@endsection