@extends('layouts.app')

@section('title', trans('nav_menu.receipt_drafts'))

@section('content')
<p class="well well-sm">
    Belum ada Draft Resi, silakan Entry Resi baru
</p>
{!! FormField::formButton(['route' => 'receipts.add-receipt'], trans('receipt.create'), ['class' => 'btn btn-default'], ['service_id' => 14, 'orig_city_id' => auth()->user()->network->origin_city_id]) !!}
{!! FormField::formButton(['route' => 'receipts.add-project-receipt'], trans('receipt.41.create'), ['class' => 'btn btn-default'], ['service_id' => 41]) !!}
@endsection