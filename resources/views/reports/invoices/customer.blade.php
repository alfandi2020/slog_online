@extends('layouts.app')

@section('content')
<h1 class="page-header">Outstanding Invoice Customer</h1>

<table class="table table-condensed">
    <thead>
        <th class="">{{ trans('app.table_no') }}</th>
        <th class="col-md-2">{{ trans('invoice.number') }}</th>
        <th class="col-md-2">{{ trans('invoice.customer') }}</th>
        <th class="col-md-2 text-right">{{ trans('invoice.amount') }}</th>
        <th class="col-md-3">{{ trans('app.date') }}</th>
        <th class="col-md-1 text-center">{{ trans('invoice.agent') }}</th>
        <th class="col-md-1 text-center">{{ trans('app.status') }}</th>
        <th class="col-md-1">{{ trans('app.action') }}</th>
    </thead>
    <tbody>
        <?php $sumTotal = 0; ?>
        @forelse($invoices as $key => $invoice)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $invoice->number }}</td>
            <td>{{ $invoice->customer->name }}</td>
            <td class="text-right">{{ formatRp($total = $invoice->connotes->sum('bill_amount')) }}</td>
            <td>
                <ul class="list-unstyled">
                    <li>Dibuat : {{ $invoice->date }}</li>
                    <li>Jth Tempo : {{ $invoice->end_date }}</li>
                    <li>Dikirim : {{ $invoice->sent_date }}</li>
                </ul>
            </td>
            <td class="text-center">{{ $invoice->agent->name }}</td>
            <td class="text-center">{!! $invoice->present()->statusLabel !!}</td>
            <td>
                {!! link_to_route('invoices.' . $invoice->type . '.show',trans('app.show'),[$invoice->number],['class'=>'btn btn-info btn-xs','title'=>'Lihat Detail ' . $invoice->number,'target'=>'_blank']) !!}
                {{-- {!! link_to_route('invoices.' . $invoice->type . '.print',trans('app.print'),[$invoice->number],['class'=>'btn btn-info btn-xs','title'=>'Cetak ' . $invoice->number,'target'=>'_blank']) !!} --}}
            </td>
        </tr>
        <?php
        $sumTotal += $total;
        ?>
        @empty
        <tr>
            <td colspan="9">{{ trans('invoice.no_invoices') }}</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-right">Jumlah</th>
            <th class="text-right">{{ formatRp($sumTotal) }}</th>
            <td colspan="5">&nbsp;</td>
        </tr>
    </tfoot>
</table>
@endsection