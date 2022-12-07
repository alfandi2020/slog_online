@if (Request::get('action') == 'create')
    {!! Form::open(['route' => 'payment-methods.store']) !!}
    {!! FormField::text('name', ['label' => trans('payment_method.name'), 'required' => true]) !!}
    {!! FormField::textarea('description', ['label' => trans('payment_method.description')]) !!}
    {!! Form::submit(trans('payment_method.create'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('payment-methods.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'edit' && $editablePaymentMethod)
    {!! Form::model($editablePaymentMethod, ['route' => ['payment-methods.update', $editablePaymentMethod->id],'method' => 'patch']) !!}
    {!! FormField::text('name', ['label' => trans('payment_method.name'), 'required' => true]) !!}
    {!! FormField::textarea('description', ['label' => trans('payment_method.description')]) !!}
    {!! FormField::radios('is_active', ['Non Aktif','Aktif'], ['label' => trans('app.status'), 'required' => true]) !!}
    @if (request('q'))
        {{ Form::hidden('q', request('q')) }}
    @endif
    @if (request('page'))
        {{ Form::hidden('page', request('page')) }}
    @endif
    {!! Form::submit(trans('payment_method.update'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('payment-methods.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'delete' && $editablePaymentMethod)
    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">{{ trans('payment_method.delete') }}</h3></div>
        <div class="panel-body">
            <label class="control-label">{{ trans('payment_method.name') }}</label>
            <p>{{ $editablePaymentMethod->name }}</p>
            {!! $errors->first('payment_method_id', '<span class="form-error small">:message</span>') !!}
        </div>
        <hr style="margin:0">
        <div class="panel-body">{{ trans('app.delete_confirm') }}</div>
        <div class="panel-footer">
            {!! FormField::delete(
                ['route'=>['payment-methods.destroy',$editablePaymentMethod->id]],
                trans('app.delete_confirm_button'),
                ['class'=>'btn btn-danger'],
                [
                    'payment_method_id' => $editablePaymentMethod->id,
                    'page' => request('page'),
                    'q' => request('q'),
                ]
            ) !!}
            {{ link_to_route('payment-methods.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
        </div>
    </div>
@endif
