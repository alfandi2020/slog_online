<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('pickup.delete') }}</h3></div>
    <div class="panel-body">
        <label class="control-label">{{ trans('pickup.number') }}</label>
        <p>{{ $pickup->number }}</p>
        <label class="control-label">{{ trans('pickup.courier') }}</label>
        <p>{{ $pickup->courier->name }}</p>
        <label class="control-label">{{ trans('app.date') }}</label>
        <p>{{ $pickup->created_at->format('Y-m-d') }}</p>
        {!! $errors->first('pickup_id', '<span class="form-error small">:message</span>') !!}
    </div>
    <hr style="margin:0">
    <div class="panel-body">{{ trans('app.delete_confirm') }}</div>
    <div class="panel-footer">
        {!! FormField::delete(
            ['route'=>['pickups.destroy',$pickup->id]],
            trans('app.delete_confirm_button'),
            ['class'=>'btn btn-danger'],
            [
                'pickup_id' => $pickup->id,
                'page' => request('page'),
                'q' => request('q'),
            ]
        ) !!}
        {{ link_to_route('pickups.edit', trans('app.cancel'), [$pickup], ['class' => 'btn btn-default']) }}
    </div>
</div>
