@inject('customersList', 'App\Entities\Customers\Customer')
@extends('layouts.app')

@section('title', trans('manifest.accountings.create'))

@section('content')
<h3 class="page-header">{{ trans('manifest.accountings.create') }}</h3>
<div class="col-sm-4">
{{ Form::open(['route' => 'manifests.accountings.store']) }}
{!! FormField::textDisplay(trans('network.network'), auth()->user()->network->name) !!}
{!! FormField::select(
    'customer_id',
    $customersList::orderBy('name')->pluck('name','id'),
    ['label' => trans('customer.customer'), 'required' => true]
) !!}
{!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
{{ Form::submit(trans('manifest.accountings.create'), ['class' => 'btn btn-success']) }}
{{ link_to_route('manifests.accountings.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
{{ Form::close() }}
</div>
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
    $('#customer_id').select2();
})()
</script>
@endsection
