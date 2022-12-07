@extends('layouts.app')

@section('title', trans('rate.delete'))

@section('content')
<h2 class="page-header">
    <div class="pull-right">
        {!! FormField::delete(['route'=>['rates.destroy',$rate->id]], trans('rate.delete'), ['class'=>'btn btn-danger'], ['rate_id'=>$rate->id]) !!}
    </div>
    {{ trans('app.delete_confirm') }}
    {!! link_to_route('rates.edit', trans('app.cancel'), [$rate->id], ['class' => 'btn btn-default']) !!}
</h2>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('rate.show') }}</h3></div>
            <div class="panel-body">
                <table class="table table-condensed">
                    <tbody>
                        <tr><th>{{ trans('rate.origin') }}</th><td>{{ $rate->originName() }}</td></tr>
                        <tr><th>{{ trans('rate.destination') }}</th><td>{{ $rate->destinationName() }}</td></tr>
                        <tr><th>{{ trans('service.service') }}</th><td>{{ $rate->service() }}</td></tr>
                        <tr><th>{{ trans('rate.rate_kg') }}</th><td class="text-right">{{ formatRp($rate->rate_kg) }}</td></tr>
                        <tr><th>{{ trans('rate.rate_pc') }}</th><td class="text-right">{{ formatRp($rate->rate_pc) }}</td></tr>
                        <tr><th>{{ trans('rate.etd') }}</th><td>{{ $rate->etd }}</td></tr>
                        <tr><th>{{ trans('rate.notes') }}</th><td>{{ $rate->notes }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection