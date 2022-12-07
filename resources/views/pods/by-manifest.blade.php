@extends('layouts.app')
@section('title', trans('pod.entry_by_manifest'))

@section('content')

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm']) !!}
<div class="form-group">
    {!! Form::label('manifest_number', trans('manifest.number'), ['class'=>'control-label']) !!}
    {!! Form::text('manifest_number', Request::get('manifest_number'), ['class'=>'form-control','required']) !!}
    {!! Form::submit(trans('manifest.search'), ['class'=>'btn btn-info']) !!}
    {!! link_to_route('pods.by-manifest', trans('app.reset'), [], ['class' => 'btn btn-default']) !!}
</div>
@can ('receive-distribution', $manifest)
    {!! link_to_route('pods.by-manifest', trans('manifest.receive'), [
        'manifest_number' => $manifest->number,
        'action' => 'receive',
    ], ['class' => 'btn btn-success pull-right']) !!}
@endcan
{!! Form::close() !!}

@if (!is_null($manifest))
<div class="row">
    <div class="col-md-4">
        @include('manifests.partials.manifest-data')
    </div>
    <div class="col-md-6 col-md-offset-1">

        @if (request('action') == 'receive')
            @include('pods.partials.receive-manifest')
        @endif

        @if ($manifest->isSent() == false)
            <div class="alert alert-warning">
                Belum dapat <strong>Entry POD</strong> karena manifest <strong>Belum Dikirim</strong>.
            </div>
        @else
            @if (!is_null($receipt))
                @include('pods.partials.pod-entry')
            @else
                @if (request('action') != 'receive')
                <div class="alert alert-info">
                    Silakan memilih receipt pada tabel dibawah untuk Entry POD.
                </div>
                @endif
            @endif
        @endif

    </div>
</div>
@else
<div class="alert alert-{{ $infoClass }}">
    {!! $info !!}
</div>
@endif

@if (!is_null($manifest))
    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title">{{ trans('manifest.receipt_lists') }}</h3></div>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th class="col-md text-center">{{ trans('app.table_no') }}</th>
                    <th class="col-md-2">{{ trans('receipt.number') }}</th>
                    <th class="col-md-2">{{ trans('receipt.consignor') }}</th>
                    <th class="col-md-2">{{ trans('receipt.consignee') }}</th>
                    <th class="col-md-1 text-center">{{ trans('receipt.items_count') }}</th>
                    <th class="col-md-1 text-right">{{ trans('receipt.weight') }}</th>
                    <th class="col-md-1 text-center">{{ trans('app.status') }}</th>
                    <th class="col-md-2">{{ trans('receipt.consignee') }}</th>
                    <th class="col-md-1">{{ trans('app.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($manifest->receipts as $key => $receipt)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $receipt->number }}</td>
                    <td>{{ $receipt->consignor['name'] }}</td>
                    <td>{{ $receipt->consignee['name'] }}</td>
                    <td class="text-center">{{ $receipt->items_count }}</td>
                    <td class="text-right">{{ $receipt->weight }}</td>
                    <td class="text-center">{!! $receipt->present()->statusLabel !!}</td>
                    <td>
                        @if ($receipt->isDelivered())
                        {{ $receipt->consignee['recipient'] }}
                        @endif
                    </td>
                    <td>
                        @if ($manifest->isSent())
                        @if (! $receipt->isDelivered())
                        {!! link_to_route('pods.by-manifest', trans('pod.create'), [
                            'manifest_number' => $manifest->number,
                            'receipt_number' => $receipt->number
                        ], ['class' => 'btn btn-info btn-xs', 'id' => 'pod-entry-' . $receipt->id]) !!}
                        @else
                        {!! link_to_route('receipts.show', trans('app.show'), [$receipt->number], [
                            'class' => 'btn btn-success btn-xs',
                            'target'=>'_blank'
                        ]) !!}
                        @endif
                        @else
                            {!! link_to_route('receipts.show', trans('app.show'), [$receipt->number], [
                                'class' => 'btn btn-success btn-xs',
                                'target'=>'_blank'
                            ]) !!}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

@section('ext_css')
    {!! Html::style(url('css/plugins/jquery.datetimepicker.css')) !!}
@endsection

@push('ext_js')
    {!! Html::script(url('js/plugins/jquery.datetimepicker.js')) !!}
@endpush

@section('script')
<script>
(function() {
    $('#time,#deliver_at,#received_at').datetimepicker({
        format:'Y-m-d H:i'
    });
})();
</script>
@endsection