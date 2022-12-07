<?php $doneRoute = $doneRoute ?? false ?>
{{-- Add Remove Resi --}}
<div class="well well-sm" id="add-remove-receipt">
    <div class="row">
        <div class="col-md-6">
            {!! Form::open(['route'=>[$assignRoute, $manifestId], 'id' => 'formChecker', 'style' => 'margin-bottom:1rem;']) !!}
            {!! FormField::text('receipt_number_a', [
                'placeholder' => trans('receipt.number'),
                'label' => trans('manifest.add_receipt'),
                'autofocus' => true,
            ]) !!}
            {!! Form::submit(trans('manifest.add_receipt'), ['class'=>'btn btn-info btn-sm']) !!}
            {!! Form::close() !!}
            @if ($doneRoute)
            {{ link_to_route($doneRoute, 'Done', [$manifest->number], ['class' => 'btn btn-default pull-right']) }}
            @endif
            {!! Form::open(['route'=>[$removeRoute, $manifestId], 'style' => 'margin-bottom:1rem;']) !!}
            {!! FormField::text('receipt_number_r', [
                'placeholder' => trans('receipt.number'),
                'label' => trans('manifest.remove_receipt'),
            ]) !!}
            {!! Form::submit(trans('manifest.remove_receipt'), ['class'=>'btn btn-warning btn-sm']) !!}
            {!! Form::close() !!}
        </div>
        <div class="col-md-6">
            <divv id="scannerLabel" style="display:none;font-weight:bold;text-align:center;">Scan QR/Barcode</divv>
            <video id="video" style="width:100%; height:auto;"></video>
        </div>
    </div>
</div>
