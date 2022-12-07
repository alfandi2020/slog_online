<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="{{ Request::segment(3) == null ? 'active' : '' }}">
        {!! link_to_route('receipts.show', trans('receipt.show'), [$receipt->number]) !!}
    </li>
    <li class="{{ Request::segment(3) == 'progress' ? 'active' : '' }}">
        {!! link_to_route('receipts.progress', trans('receipt.progress'), [$receipt->number]) !!}
    </li>
    <li class="{{ Request::segment(3) == 'manifests' ? 'active' : '' }}">
        {!! link_to_route('receipts.manifests', trans('receipt.manifests'), [$receipt->number]) !!}
    </li>
    <li class="{{ Request::segment(3) == 'couriers' ? 'active' : '' }}">
        {!! link_to_route('receipts.couriers', trans('receipt.couriers'), [$receipt->number]) !!}
    </li>
    <li class="{{ Request::segment(3) == 'costs-detail' ? 'active' : '' }}">
        {!! link_to_route('receipts.costs-detail', trans('receipt.costs_detail'), [$receipt->number]) !!}
    </li>
    @if(! is_null($receipt->items_detail) && $count = count($receipt->items_detail))
    <li class="{{ Request::segment(3) == 'items' ? 'active' : '' }}">
        {!! link_to_route('receipts.items', trans('receipt.items') . ' (' . $count . ')', [$receipt->number]) !!}
    </li>
    @endif
    @if($receipt->isDelivered())
    <li class="{{ Request::segment(3) == 'pod' ? 'active' : '' }}">
        {!! link_to_route('receipts.pod', trans('pod.pod_full'), [$receipt->number]) !!}
    </li>
    @endif
</ul>
<br>
