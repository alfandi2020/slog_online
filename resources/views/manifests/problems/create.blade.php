@extends('layouts.app')

@section('title', trans('manifest.problems.create'))

@section('content')

<p class="well well-sm">
    Membuat Manifest Problem Resi untuk mengembalikan resi yang <span class="label label-danger">Not OK</span>, problem, atau kurang lengkap.
    @if ($refManifest)
    Pada manifest <strong>{{ $refManifest->present()->numberLink() }}</strong>.
    @endif
</p>

{!! Form::open(['route' => ['manifests.problems.store']]) !!}
<div class="row">
    <div class="col-md-9">
        @include('manifests.partials.problem-receipt-lists')
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                {!! FormField::select('handler_id', App\Entities\Users\User::orderBy('name')->pluck('name', 'id'), [
                    'value' => $refManifest ? $refManifest->creator_id : '',
                    'label' => 'Ditujukan Kepada',
                    'placeholder' => 'Pilih User',
                    'required' => true,
                ]) !!}
                {!! FormField::textarea('notes',['label'=> trans('app.notes'),'rows' => 4]) !!}
            </div>
            <div class="panel-footer">
                {!! Form::submit(trans('manifest.problems.create'), ['class'=>'btn btn-primary']) !!}
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}

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
    $('#handler_id').select2();

    $('#select-all').click(function () {
        $('.select-me').prop('checked', this.checked);
    });

    $('.receipt-list tr').click(function(event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

    $('.select-me').change(function () {
        var check = ($('.select-me').filter(":checked").length == $('.select-me').length);
        $('#select-all').prop("checked", check);
    });
})();
</script>
@endsection