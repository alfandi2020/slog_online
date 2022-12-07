@extends('layouts.app')

@section('title', trans('package_type.list'))

@section('content')
<h2 class="page-header">
    {{ link_to_route('admin.package-types.index', trans('package_type.create'), ['action' => 'create'], ['class' => 'btn btn-success pull-right']) }}
    {{ trans('package_type.list') }}
</h2>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default table-responsive">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('package_type.name') }}</th>
                        <th class="text-center">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packageTypes as $key => $packageType)
                    <tr>
                        <td class="text-center">{{ 1 + $key }}</td>
                        <td>{{ $packageType->name }}</td>
                        <td class="text-center">
                            {!! link_to_route('admin.package-types.index', trans('app.edit'), ['action' => 'edit', 'id' => $packageType->id], ['id' => 'edit-package_type-' . $packageType->id]) !!} |
                            {!! link_to_route('admin.package-types.index', trans('app.delete'), ['action' => 'delete', 'id' => $packageType->id], ['id' => 'del-package_type-' . $packageType->id]) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        @if (Request::get('action') == 'create')
            {!! Form::open(['route' => 'admin.package-types.store']) !!}
            {!! FormField::text('name') !!}
            {!! Form::submit(trans('package_type.create'), ['class' => 'btn btn-success']) !!}
            {!! Form::hidden('cat', 'pack_type') !!}
            {{ link_to_route('admin.package-types.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
        @endif
        @if (Request::get('action') == 'edit' && $editableType)
            {!! Form::model($editableType, ['route' => ['admin.package-types.update', $editableType->id],'method' => 'patch']) !!}
            {!! FormField::text('name') !!}
            {!! Form::hidden('cat') !!}
            {!! Form::submit(trans('package_type.update'), ['class' => 'btn btn-success']) !!}
            {{ link_to_route('admin.package-types.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
        @endif
        @if (Request::get('action') == 'delete' && $editableType)
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">{{ trans('package_type.delete') }}</h3></div>
                <div class="panel-body">
                    <label class="control-label">{{ trans('package_type.name') }}</label>
                    <p>{{ $editableType->name }}</p>
                </div>
                <hr style="margin:0">
                <div class="panel-body">{{ trans('app.delete_confirm') }}</div>
                <div class="panel-footer">
                    {!! FormField::delete(['route'=>['admin.package-types.destroy',$editableType->id]], trans('app.delete_confirm_button'), ['class'=>'btn btn-danger'], ['package_type_id'=>$editableType->id]) !!}
                    {{ link_to_route('admin.package-types.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection