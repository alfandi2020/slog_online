<!-- Nav tabs -->
<ul class="nav nav-tabs bar_tabs">
    <li class="{{ Request::segment(4) == null ? 'active' : '' }}">
        {!! link_to_route('admin.networks.show', trans('network.show'), [$network->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'users' ? 'active' : '' }}">
        {!! link_to_route('admin.networks.users', trans('network.users'), [$network->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'delivery_units' ? 'active' : '' }}">
        {!! link_to_route('admin.networks.delivery-units', trans('network.delivery_units'), [$network->id]) !!}
    </li>
    <li class="{{ Request::segment(4) == 'customers' ? 'active' : '' }}">
        {!! link_to_route('admin.networks.customers', trans('network.customers'), [$network->id]) !!}
    </li>
</ul>
<br>