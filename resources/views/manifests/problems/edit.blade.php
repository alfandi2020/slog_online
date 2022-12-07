@inject('usersList', 'App\Entities\Users\User')
@extends('layouts.app')
@section('title', trans('manifest.edit').' - '.$manifest->number)

@section('content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data')
    </div>
    <div class="col-md-4">
        {!! Form::model($manifest, ['route'=>['manifests.problems.update', $manifest->number], 'method' => 'patch']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('manifest.edit') }}</h3></div>
            <div class="panel-body">
                {!! FormField::select('handler_id', $usersList::pluck('name','id'), ['label' => trans('manifest.handler'), 'required' => true]) !!}
                {!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('manifest.update'), ['class'=>'btn btn-primary']) !!}
                {!! link_to_route('manifests.problems.show', trans('app.cancel'), [$manifest->number], ['class'=>'btn btn-default']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

{!! FormField::delete(['route'=>['manifests.destroy',$manifest->number]], trans('manifest.delete'), ['class'=>'btn btn-danger'], ['manifest_id' => $manifest->id]) !!}
@endsection

@section('ext_css')
{!! Html::style(url('css/plugins/select2.min.css')) !!}
@endsection

@push('ext_js')
{!! Html::script(url('js/plugins/select2.min.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#user_id').select2();
})()
</script>
@endsection