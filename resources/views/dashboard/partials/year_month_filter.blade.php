{{ Form::open(['method' => 'get', 'class' => 'form-inline pull-right']) }}
{{ Form::label('month', __('app.year_month'), ['class' => 'control-label']) }}
{{ Form::select('month', getMonths(), request('month', date('m')), ['class' => 'form-control input-sm']) }}
{{ Form::select('year', getYears(), request('year', date('Y')), ['class' => 'form-control input-sm']) }}
{{ Form::submit(__('app.submit'), ['class' => 'btn btn-info btn-sm']) }}
{{ Form::close() }}
