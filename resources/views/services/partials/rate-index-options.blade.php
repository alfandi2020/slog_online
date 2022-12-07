<div class="well well-sm">
    {{ Form::open(['method'=>'get','class'=>'form-inline']) }}
    {{ Form::hidden('region', Request::get('region', 3)) }}
    @if ($destCity)
    {{ Form::hidden('dest_city_id', $destCity->id) }}
    @endif
    {!! FormField::select('orig_city_id', $regionQuery->getCitiesList(), [
        'label' => 'Origin',
        'value' => $origCityId,
        'placeholder'=> false,
        'class'=> 'select2',
    ]) !!}
    {!! FormField::select('service_id', $service::ratailAndSalDropdown(), [
        'label' => false,
        'value' => $serviceId,
        'placeholder'=> false,
    ]) !!}
    {!! FormField::select('customer_id', $customers, [
        'label' => false,
        'value' => $customerId,
        'placeholder'=> false,
        'class'=> 'select2',
    ]) !!}
    {{ Form::submit(trans('app.filter'), ['class' => 'btn btn-default']) }}
    {{ link_to_route('rates.list', 'Tampilkan List', [], ['class' => 'btn btn-default pull-right']) }}
    {{ Form::close() }}
</div>
