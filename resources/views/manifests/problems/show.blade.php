@extends('layouts.manifest-detail')

@section('show-links')
    @include('manifests.partials.show-links-problem')
@endsection

@section('manifest-content')

@include('manifests.partials.manifest-stat')

<p class="well well-sm">
    @if ($nullProblemNotesExists)
    Untuk dapat mengirimkan manifest ini, silakan <strong>lengkapi alasan penolakan</strong> terlebih dahulu untuk seluruh resi.
    @else
    Alasan penolakan seluruh resi sudah lengkap, silakan mengirimkan Manifest ini.
    @endif

    @if ($manifest->notes)
    <br><strong>{{ trans('app.notes') }}</strong> : {{ $manifest->notes }}
    @endif
</p>

<div class="row">
    <div class="col-md-12">
        @if (request('action') == 'add_remove_receipt')
        @can ('add-remove-receipt-of', $manifest)
        <div class="alert alert-info">{{ trans('manifest.add_receipt_instruction') }}</div>
        @include('manifests.partials.form-add-remove-receipts', [
            'assignRoute' => 'manifests.assign-receipt',
            'removeRoute' => 'manifests.remove-receipt',
            'manifestId' => $manifest->id,
            'doneRoute' => 'manifests.problems.show',
        ])
        @endcan
        @endif

        <div class="panel panel-{{ $manifest->present()->status['class'] }} table-responsive" id="problem-receipt-list">
            <div class="panel-heading">
                @can('edit', $manifest)
                {{ link_to_route('manifests.problems.show', trans('manifest.problems.edit_reject_reasons'), [$manifest->number, 'action' => 'reason_edit', '#problem-receipt-list'], ['class' => 'pull-right']) }}
                @endcan
                <h3 class="panel-title">{{ trans('manifest.receipt_lists') }}</h3>
            </div>
            {{ Form::open(['route' => ['receipts.problem-notes-update'], 'method' => 'patch']) }}
            {{ Form::hidden('manifest_id', $manifest->id) }}
            {{ Form::hidden('manifest_number', $manifest->number) }}
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th class="text-center">{{ trans('app.table_no') }}</th>
                        <th class="col-md-1 text-center">{{ trans('app.date') }}</th>
                        <th class="col-md-2 text-center">{{ trans('receipt.number') }}</th>
                        <th class="col-md-1 text-center">{{ trans('service.service') }}</th>
                        <th class="col-md-3">{{ trans('receipt.consignor') }} / {{ trans('receipt.consignee') }}</th>
                        <th class="col-md-2 text-right">{{ trans('receipt.bill_amount') }}</th>
                        <th class="col-md-3">{{ trans('receipt.reject_reason') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($manifest->receipts as $key => $receipt)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-center">{{ $receipt->pickup_time->format('Y-m-d') }}</td>
                        <td class="text-center">
                            {{ $receipt->numberLink() }}<br>
                            <span class="badge">{{ $receipt->packType->name }}</span>
                            @if ($receipt->pivot->handler_id && $receipt->pivot->end_status == 'no')
                                <span class="label label-danger">Not OK</span>
                            @elseif ($receipt->pivot->handler_id && $receipt->pivot->end_status != 'no')
                                <span class="label label-success">OK</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $receipt->service() }}</td>
                        <td>
                            <div><strong>Dari :</strong> {{ $receipt->consignor['name'] }} ({{ $receipt->originName() }})</div>
                            <div><strong>Kepada:</strong> {{ $receipt->consignee['name'] }} ({{ $receipt->destinationName() }})</div>
                        </td>
                        <td class="text-right">{{ formatRp($receipt->bill_amount) }}</td>
                        <td>
                            @if (is_null($receipt->pivot->notes) || request('action') == 'reason_edit')
                            {!! FormField::textarea('notes['.$receipt->id.']', ['label' => false, 'value' => $receipt->pivot->notes]) !!}
                            @else
                            {{ $receipt->pivot->notes }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6">{{ trans('receipt.empty') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">{{ trans('app.total') }} :</th>
                        <th class="text-right">
                            {{ formatRp($manifest->receipts->sum('bill_amount')) }}
                        </th>
                        <th>
                            @if ($nullProblemNotesExists || request('action') == 'reason_edit')
                            {{ Form::submit(trans('manifest.problems.reject_reason_update'), ['class' => 'btn btn-success']) }}
                            @endif
                            @if (request('action') == 'reason_edit')
                            {{ link_to_route('manifests.problems.show', trans('app.cancel'), [$manifest->number], ['class' => 'btn btn-default']) }}
                            @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
            {{ Form::close() }}
        </div>
    </div>
</div>

@endsection