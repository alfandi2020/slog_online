<?php $doneRoute = $doneRoute ?? false ?>
{{-- Add Remove Resi --}}
<div class="well well-sm" id="add-remove-receipt">
    <div class="row">
        <div class="col-md-6">
            {!! Form::open(['route'=>[$assignRoute, $manifestId], 'class'=>'form-inline']) !!}
            {!! FormField::text('receipt_number_a', [
                'placeholder' => trans('receipt.number'),
                'label' => trans('manifest.add_receipt'),
                'autofocus' => true,
            ]) !!}
            {!! Form::submit(trans('manifest.add_receipt'), ['class'=>'btn btn-info btn-sm']) !!}
            {!! Form::close() !!}
        </div>
        <div class="col-md-6">
            {!! Form::open(['route'=>[$removeRoute, $manifestId], 'class'=>'form-inline']) !!}
            {!! FormField::text('receipt_number_r', [
                'placeholder' => trans('receipt.number'),
                'label' => trans('manifest.remove_receipt'),
                'style' => 'width:170px',
            ]) !!}
            {!! Form::submit(trans('manifest.remove_receipt'), ['class'=>'btn btn-warning btn-sm']) !!}
            @if ($doneRoute)
                {{ link_to_route($doneRoute, trans('app.done'), [$invoice->id], ['class' => 'btn btn-default pull-right']) }}
            @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>
