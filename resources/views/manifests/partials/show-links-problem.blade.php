@if ($manifest->isSent() == false && $nullProblemNotesExists == false)
{!! FormField::formButton(
    [
        'route' => ['manifests.problems.send', $manifest->id],
        'method' => 'patch',
        'onsubmit' => trans('manifest.send_confirm'),
    ],
    trans('manifest.send'),
    ['class' => 'btn btn-success', 'id' => 'send-manifest'],
    ['manifest_number' => $manifest->number]
) !!}
@endif

@can ('add-remove-receipt-of', $manifest)
    {{ link_to_route('manifests.problems.show', trans('manifest.add_remove_receipt'), [$manifest->number, 'action' => 'add_remove_receipt', '#add-remove-receipt'], ['class' => 'btn btn-default']) }}
@endcan


@can ('receive-problem', $manifest)
{!! FormField::formButton(
    [
        'route' => ['manifests.problems.patch-receive', $manifest->id],
        'method' => 'patch',
        'onsubmit' => trans('manifest.receive_confirm'),
    ],
    trans('manifest.receive'),
    ['class' => 'btn btn-success', 'id' => 'receive-manifest'],
    ['manifest_number' => $manifest->number]
) !!}
@endcan

@can('take-back', $manifest)
    {!! FormField::formButton(
        [
            'route' => ['manifests.take-back', $manifest->id],
            'method' => 'patch',
            'onsubmit' => trans('manifest.take_back_confirm'),
        ],
        trans('manifest.take_back'),
        ['class' => 'btn btn-default', 'id' => 'take-manifest-back'],
        ['manifest_number' => $manifest->number]) !!}
@endcan

@can('edit', $manifest)
    {{ link_to_route('manifests.problems.edit', trans('manifest.edit'), $manifest->number, ['class' => 'btn btn-warning', 'id' => 'edit-manifest']) }}
@endcan

{{--
@if($manifest->isSent())
    {!! html_link_to_route('manifests.' . str_plural($manifest->typeCode()) . '.pdf', trans('manifest.pdf'), [$manifest->number], [
        'icon' => 'print',
        'class' => 'btn btn-info',
        'target' => '_blank',
    ]) !!}
@endif
--}}
{{ link_to_route('manifests.problems.index', trans('manifest.back_to_index'), [], ['class' => 'btn btn-default']) }}
