@extends('layouts.manifest-detail')

@section('show-links')
    {!! html_link_to_route('manifests.distributions.xls', __('manifest.export_xls'), $manifest, [
        'icon' => 'file-excel-o',
        'class' => 'btn btn-default'
    ]) !!}
    @include('manifests.partials.show-links')
@endsection

@section('manifest-content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data-distribution')
        @can('send-distribution', $manifest)
        <div class="panel panel-default" id="send-manifest">
            <div class="panel-heading"><h3 class="panel-title">{{ __('manifest.send') }}</h3></div>
            <div class="panel-body">
                {{ Form::open(['route' => ['manifests.distributions.send', $manifest->id], 'method' => 'patch']) }}
                <div class="row">
                    <div class="col-sm-6">
                        {!! FormField::text('deliver_at', [
                            'label' => __('manifest.deliver_at'),
                            'placeholder' => 'yyyy-mm-dd HH:ii',
                            'class' => 'time-select',
                            'required' => true,
                        ]) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! FormField::text('start_km', ['label' => __('manifest.start_km')]) !!}
                    </div>
                </div>
                {!! FormField::textarea('notes', ['label' => __('app.notes')]) !!}
                {{ Form::submit(__('manifest.send'), ['class' => 'btn btn-success']) }}
                {{ Form::close() }}
            </div>
        </div>
        @endcan
    </div>
    <div class="col-md-8">
        @can ('add-remove-receipt-of', $manifest)
        <div class="alert alert-warning">
            {{ __('manifest.add_receipt_instruction') }}
            @if ($manifest->dest_city_id)
            untuk tujuan <strong style="font-size:18px">{!! $manifest->destinationCity->name !!}</strong>.
            <ul class="list-unstyled">
                <li>* Pastikan Resi sesuai dengan Kota/Kab. tujuan.</li>
                <li>* <a href="#send-manifest">Kirim Manifest</a> jika sudah selesai mengisi Resi.</li>
            </ul>
            @endif
        </div>
        @include('manifests.partials.form-add-remove-receipts', [
            'assignRoute' => 'manifests.assign-receipt',
            'removeRoute' => 'manifests.remove-receipt',
            'manifestId' => $manifest->id,
        ])
        @endcan

        @include('manifests.partials.receipt-lists', ['receipts' => $manifest->receipts, 'class' => $manifest->present()->status['class']])
    </div>
</div>
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
    $('.time-select').datetimepicker({
        format:'Y-m-d H:i',
        closeOnTimeSelect: true
    });
})();
</script>
@endsection
