{{ Form::model($manifest, ['route' => ['pods.receive-manifest', $manifest], 'method' => 'patch']) }}
<legend>{{ trans('manifest.receive') }}</legend>
{!! FormField::textDisplay(trans('manifest.courier'), $manifest->courier->name) !!}
<div class="row">
    <div class="col-md-6">
        {!! FormField::text('deliver_at', [
            'label' => trans('manifest.deliver_at'),
            'value' => $manifest->deliver_at->format('Y-m-d H:i')
        ]) !!}
    </div>
    <div class="col-md-6">{!! FormField::text('received_at', ['label' => trans('manifest.received_at')]) !!}</div>
</div>
<div class="row">
    <div class="col-md-6">{!! FormField::text('start_km', ['label' => trans('manifest.start_km')]) !!}</div>
    <div class="col-md-6">{!! FormField::text('end_km', ['label' => trans('manifest.end_km')]) !!}</div>
</div>
{!! FormField::textarea('notes', ['label' => trans('app.notes')]) !!}
{{ Form::submit(trans('manifest.receive'), ['class' => 'btn btn-success']) }}
{!! link_to_route('pods.by-manifest', trans('app.cancel'), ['manifest_number' => $manifest->number], ['class' => 'btn btn-default']) !!}
{{ Form::close() }}
<hr>