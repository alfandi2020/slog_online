@extends('layouts.manifest-detail')

@section('show-links')
    @include('manifests.partials.show-links')
@endsection

@section('manifest-content')

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data')
    </div>
    <div class="col-md-8">
        @can ('add-remove-receipt-of', $manifest)
        <div class="alert alert-info">{{ trans('manifest.add_receipt_instruction') }}</div>
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