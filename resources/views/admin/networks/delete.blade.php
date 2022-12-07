@extends('layouts.app')

@section('title', trans('network.delete'))

@section('content')
<h2 class="page-header">
    <div class="pull-right">
        {!! FormField::delete(['route'=>['admin.networks.destroy',$network->id]], trans('network.delete'), [
            'class'=>'btn btn-danger'
        ], ['network_id' => $network->id]) !!}
    </div>
    {{ trans('app.delete_confirm') }}
    {!! link_to_route('admin.networks.edit', trans('app.cancel'), [$network->id], ['class' => 'btn btn-default']) !!}
</h2>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('network.show') }}</h3></div>
            <table class="table table-condensed">
                <tbody>
                    <tr><th>{{ trans('network.name') }}</th><td>{{ $network->name }}</td></tr>
                    <tr><th>{{ trans('network.description') }}</th><td>{{ $network->description }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection