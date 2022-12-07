@extends('layouts.app')

@section('title', trans('comodity.list'))

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default table-responsive">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('comodity.name') }}</th>
                        <th class="text-center">{{ trans('comodity.customers_count') }}</th>
                        <th class="text-center">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $customersTotal = 0 ?>
                    @foreach($comodities as $key => $comodity)
                    <tr>
                        <td class="text-center">{{ 1 + $key }}</td>
                        <td>{{ $comodity->name }}</td>
                        <td class="text-center">{{ $comodity->customers_count }}</td>
                        <td class="text-center">
                            {!! link_to_route('admin.comodities.index', trans('app.edit'), ['action' => 'edit', 'id' => $comodity->id], ['id' => 'edit-comodity-' . $comodity->id]) !!} |
                            {!! link_to_route('admin.comodities.index', trans('app.delete'), ['action' => 'delete', 'id' => $comodity->id], ['id' => 'del-comodity-' . $comodity->id]) !!}
                        </td>
                    </tr>
                    <?php $customersTotal += $comodity->customers_count; ?>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center">{{ trans('app.total') }}</td>
                        <th class="text-center">{{ $customersTotal }}</th>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        @if (! Request::has('action'))
        {{ link_to_route('admin.comodities.index', trans('comodity.create'), ['action' => 'create'], ['class' => 'btn btn-success pull-right']) }}
        @endif
        @if (Request::get('action') == 'create')
            {!! Form::open(['route' => 'admin.comodities.store']) !!}
            {!! FormField::text('name') !!}
            {!! Form::submit(trans('comodity.create'), ['class' => 'btn btn-success']) !!}
            {!! Form::hidden('cat', 'comodity') !!}
            {{ link_to_route('admin.comodities.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
        @endif
        @if (Request::get('action') == 'edit' && $editableComodity)
            {!! Form::model($editableComodity, ['route' => ['admin.comodities.update', $editableComodity->id],'method' => 'patch']) !!}
            {!! FormField::text('name') !!}
            {!! Form::hidden('cat') !!}
            {!! Form::submit(trans('comodity.update'), ['class' => 'btn btn-success']) !!}
            {{ link_to_route('admin.comodities.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
        @endif
        @if (Request::get('action') == 'delete' && $editableComodity)
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('comodity.delete') }}</h3></div>
                <div class="panel-body">
                    <label class="control-label">{{ trans('comodity.name') }}</label>
                    <p>{{ $editableComodity->name }}</p>
                    {!! $errors->first('comodity_id', '<span class="form-error small">:message</span>') !!}
                </div>
                <hr style="margin:0">
                <div class="panel-body">{{ trans('app.delete_confirm') }}</div>
                <div class="panel-footer">
                    {!! FormField::delete(['route'=>['admin.comodities.destroy',$editableComodity->id]], trans('app.delete_confirm_button'), ['class'=>'btn btn-danger'], ['comodity_id'=>$editableComodity->id]) !!}
                    {{ link_to_route('admin.comodities.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection