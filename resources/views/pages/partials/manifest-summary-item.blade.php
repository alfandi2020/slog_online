<?php
$icon = isset($icon) ? $icon : 'tasks';
$number = isset($number) ? $number : 0;
$class = $number ? 'success' : 'warning';
$text = isset($text) ? $text : 'Info Text';
$linkRoute = isset($linkRoute) ? $linkRoute : '#';
?>

<a href="{{ $linkRoute }}"
    class="list-group-item {{ $number ? 'list-group-item-info' : '' }}"
    title="{{ $text }}"
>
    <i class="fa fa-{{ $icon }} fa-fw" title="trans('manifest.handover_out')"></i> {{ $text }}
    <span class="pull-right text-muted"><em>{{ $number }}</em></span>
</a>