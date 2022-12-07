
<!-- Nav tabs -->
<ul class="nav nav-tabs bar_tabs">
    @foreach($regions as $key => $region)
    <?php
    if ($loop->first)
        $activeClass = Request::get('region') == $key || Request::get('region') == null ? 'active' : '';
    else
        $activeClass = Request::get('region') == $key ? 'active' : '';
    ?>
    <li class="{{ $activeClass }}">
        {!! link_to_route('rates.index', $region, ['region' => $key] + Request::only('orig_city_id','service_id','customer_id')) !!}
    </li>
    @endforeach
</ul>
<br>