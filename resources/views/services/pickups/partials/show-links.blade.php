{{-- Take pickup back from on pickup status --}}
@can ('take-back', $pickup)
    {!! FormField::formButton([
        'route'=> ['pickups.take-back', $pickup],
        'onsubmit' => trans('pickup.take_back_confirm', ['number' => $pickup->number]),
        'method' => 'patch',
    ], trans('pickup.take_back'), ['class'=>'btn btn-warning']) !!}
@endcan

@can ('update', $pickup)
{!! link_to_route('pickups.edit', trans('pickup.edit'), [$pickup], ['class' => 'btn btn-warning']) !!}
@endcan

@can ('cancel-returned', $pickup)
    {!! FormField::formButton([
        'route'=> ['pickups.cancel-returned', $pickup],
        'onsubmit' => trans('pickup.cancel_returned_confirm', ['number' => $pickup->number]),
        'method' => 'delete',
    ], trans('pickup.cancel_returned'), ['class'=>'btn btn-danger']) !!}
@endcan

@can ('receive', $pickup)
{!! link_to_route('pickups.receive', trans('pickup.receive'), [$pickup], ['class' => 'btn btn-info']) !!}
@endcan
{!! link_to_route('pickups.pdf', trans('pickup.pdf'), [$pickup], ['class' => 'btn btn-default']) !!}
{!! link_to_route('pickups.index', trans('pickup.back_to_index'), [], ['class' => 'btn btn-default']) !!}
