<?php
$icon = isset($icon) ? $icon : 'tasks';
$number = isset($number) ? $number : 0;
$class = $number ? 'success' : 'warning';
$text = isset($text) ? $text : 'Info Text';
$linkText = isset($linkText) ? $linkText : $text;
$linkRoute = isset($linkRoute) ? $linkRoute : '#';
?>

<a href="{{ $linkRoute }}" title="{{ $text }}">
    <div class="panel panel-{{ $class }}">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-3">
                    <i class="fa fa-{{ $icon }} fa-4x"></i>
                </div>
                <div class="col-xs-9 text-right">
                    <div class="lead" style="margin-bottom:0">
                        @if ($number) ({{ $number }}) @endif
                        {{ $linkText }}
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <span class="pull-left">
                @if ($number)
                    <strong>{{ $number }} Manifest Baru</strong>
                @else
                    Lihat List Manifest
                @endif
            </span>
            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
            <div class="clearfix"></div>
        </div>
    </div>
</a>