@extends('layouts.app')
@section('title', trans('manifest.show'))

@section('content')
<div class="pull-right">
    @can('receive', $manifest)
    <?php
        $uncheckedReceipts = [];
        foreach ($manifest->receipts as $receipt) {
            if (is_null($receipt->pivot->handler_id)) {
                $uncheckedReceipts[] = $receipt->id;
            }
        }
    ?>
    {{-- TODO: Add receive end_km property to Distribution Manifest Receive --}}
    @if (count($uncheckedReceipts) == 0)
    {!! FormField::formButton([
            'route' => ['manifests.patch-receive', $manifest->id],
            'method' => 'patch',
            'onsubmit' => trans('manifest.receive_confirm'),
        ],
        trans('manifest.receive'),
        ['class' => 'btn btn-success', 'id' => 'receive_manifest'],
        ['manifest_number' => $manifest->number]) !!}
    @endif
    @endcan
</div>
<h2 class="page-header">{{ $manifest->number }} <small>{{ trans('manifest.receive') }}</small></h2>

<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data')
    </div>
    <div class="col-md-8">
        @can ('receive', $manifest)
        @if (count($uncheckedReceipts) == 0)
        <div class="alert alert-success">Resi sudah lengkap, silakan <strong>Terima Manifest</strong></div>
        @else
        <div class="alert alert-info">Silakan Scan No Resi untuk Cek kelengkapan</div>
        @endif
        <div class="well well-sm">
            <div class="row">
                <div class="col-md-6">
                {!! Form::open(['route'=>['manifests.check-receipt', $manifest->id], 'style' => 'margin-bottom:1rem;', 'method' => 'patch', 'id'=>'formChecker']) !!}
                    {!! FormField::text('receipt_number_a', [
                        'placeholder' => trans('receipt.number'),
                        'label' => trans('manifest.check_receipt'),
                        'autofocus' => true,
                    ]) !!}
                    {!! Form::submit(trans('manifest.check_receipt'), ['class'=>'btn btn-info btn-sm']) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['route'=>['manifests.reject-receipt', $manifest->id], 'style' => 'margin-bottom:1rem;', 'method' => 'patch']) !!}
                    {!! FormField::text('receipt_number_r', [
                        'placeholder' => trans('receipt.number'),
                        'label' => trans('manifest.reject_receipt'),
                        'autofocus' => true,
                    ]) !!}
                    {!! Form::submit(trans('manifest.reject_receipt'), ['class'=>'btn btn-danger btn-sm']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="col-md-6">
                    <divv id="scannerLabel" style="display:none;font-weight:bold;text-align:center;">Scan QR/Barcode</divv>
                    <video id="video" style="width:100%; height:auto;"></video>
                </div>
            </div>
        </div>
        @endcan

        @include('manifests.partials.receipt-lists', ['receipts' => $manifest->receipts])
    </div>
</div>

@endsection