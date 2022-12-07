@php
    $manifestType = str_plural($manifest->typeCode());
@endphp
@can('take-back', $manifest)
    {!! FormField::formButton(
        [
            'route' => ['manifests.take-back', $manifest->id],
            'method' => 'patch',
            'onsubmit' => __('manifest.take_back_confirm'),
        ],
        __('manifest.take_back'),
        ['class' => 'btn btn-default'],
        ['manifest_number' => $manifest->number]) !!}
@endcan
@can('edit', $manifest)
    {{ link_to_route('manifests.'.$manifestType.'.edit', __('manifest.edit'), $manifest->number, ['class' => 'btn btn-warning', 'id' => 'edit-manifest']) }}
@endcan
@can('send', $manifest)
    {!! FormField::formButton([
            'route' => ['manifests.send', $manifest->id],
            'method' => 'patch',
            'onsubmit' => __('manifest.send_confirm'),
        ],
        __('manifest.send'),
        ['class' => 'btn btn-success'],
        ['manifest_number' => $manifest->number]) !!}
@endcan
@can('receive', $manifest)
    {{ link_to_route('manifests.receive', __('manifest.receive'), [$manifest->number], ['class' => 'btn btn-info']) }}
@endcan
@can('print', $manifest)
    {!! html_link_to_route('manifests.'.$manifestType.'.pdf', __('manifest.pdf'), [$manifest->number], [
        'icon' => 'print',
        'class' => 'btn btn-info',
        'target' => '_blank',
    ]) !!}
@endcan
@can('pod', $manifest)
    {!! html_link_to_route('pods.by-manifest', __('pod.create'), ['manifest_number' => $manifest->number], [
        'icon' => 'check-square-o',
        'class' => 'btn btn-success',
    ]) !!}
@endcan
{{ link_to_route('manifests.'.$manifestType.'.index', __('manifest.back_to_index'), [], ['class' => 'btn btn-default']) }}
