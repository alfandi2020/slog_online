@extends('layouts.app')

@section('title')
{{ trans('report.account_receivables') }}
({{ $invoices->count() }} {{ trans('invoice.invoice') }})
Per {{ dateId($perDate) }} (Dalam Rupiah)
@endsection

@section('content')

{!! Form::open(['method'=>'get','class'=>'form-inline well well-sm text-center hidden-print']) !!}
{!! FormField::text('per_date', ['label' => trans('app.per_date'), 'value' => $perDate, 'style' => 'width:100px']) !!}
{!! Form::submit('Lihat Daftar Piutang', ['class'=>'btn btn-info']) !!}
{!! link_to_route('reports.invoices.receivables', 'Per Hari ini', [], ['class'=>'btn btn-default']) !!}
{!! Form::close() !!}

<h3 class="visible-print text-center">
    {{ trans('report.account_receivables') }} Per {{ dateId($perDate) }}
    <small>(Dalam Rupiah)</small>
</h3>

<div class="table-responsive">
    <table class="table table-condensed table-bordered" style="font-size: 12px">
        <thead>
            <tr>
                <th style="width: 3%" class="text-center">{{ trans('app.table_no') }}</th>
                <th style="width: 25%">{{ trans('invoice.customer') }}</th>
                <th style="width: 8%" class="text-center">{{ trans('invoice.number') }}</th>
                <th style="width: 8%" class="text-center">{{ trans('app.date') }}</th>
                <th style="width: 8%" class="text-center">{{ trans('invoice.end_date') }}</th>
                <th style="width: 8%" class="text-center">1-30 <span class="text-muted">Hari</span></th>
                <th style="width: 8%" class="text-center">31 - 60 <span class="text-muted">Hari</span></th>
                <th style="width: 8%" class="text-center">61 - 90 <span class="text-muted">Hari</span></th>
                <th style="width: 8%" class="text-center">91 - 120 <span class="text-muted">Hari</span></th>
                <th style="width: 8%" class="text-center">> 120 <span class="text-muted">Hari</span></th>
                <th style="width: 8%" class="text-center">{{ trans('app.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total1Amount = 0;
                $total2Amount = 0;
                $total3Amount = 0;
                $total4Amount = 0;
                $total5Amount = 0;
                $total = 0;
            @endphp
            @forelse($invoices->groupBy('customer_id') as $key => $groupedInvoice)
            @php
                $sum1Amount = 0;
                $sum2Amount = 0;
                $sum3Amount = 0;
                $sum4Amount = 0;
                $sum5Amount = 0;
                $customerTotal = 0;
            @endphp
                @foreach($groupedInvoice as $key => $invoice)
                @php
                $diffInDays = Carbon::parse($invoice->date)->diffInDays(Carbon::parse($perDate));
                @endphp
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $invoice->customer->present()->numberName }}</td>
                    <td class="text-center">{{ $invoice->present()->numberLink }}</td>
                    <td class="text-center">{{ $invoice->date }}</td>
                    <td class="text-center">{{ $invoice->end_date }}</td>
                    @php
                    $amount1 = 0;
                    if ($diffInDays <= 30) {
                        // $amount1 = $invoice->receipts->sum('bill_amount');
                        $amount1 = $invoice->getAmount();
                    }
                    @endphp
                    <td class="{{ $amount1 ? 'text-right' : 'text-center' }}">{{ formatNo($amount1) }}</td>
                    @php
                    $amount2 = 0;
                    if ($diffInDays > 30 && $diffInDays <= 60) {
                        // $amount2 = $invoice->receipts->sum('bill_amount');
                        $amount2 = $invoice->getAmount();
                    }
                    @endphp
                    <td class="{{ $amount2 ? 'text-right' : 'text-center' }}">{{ formatNo($amount2) }}</td>
                    @php
                    $amount3 = 0;
                    if ($diffInDays > 60 && $diffInDays <= 90) {
                        // $amount3 = $invoice->receipts->sum('bill_amount');
                        $amount3 = $invoice->getAmount();
                    }
                    @endphp
                    <td class="{{ $amount3 ? 'text-right' : 'text-center' }}">{{ formatNo($amount3) }}</td>
                    @php
                    $amount4 = 0;
                    if ($diffInDays > 90 && $diffInDays <= 120) {
                        // $amount4 = $invoice->receipts->sum('bill_amount');
                        $amount4 = $invoice->getAmount();
                    }
                    @endphp
                    <td class="{{ $amount4 ? 'text-right' : 'text-center' }}">{{ formatNo($amount4) }}</td>
                    @php
                    $amount5 = 0;
                    if ($diffInDays > 120) {
                        // $amount5 = $invoice->receipts->sum('bill_amount');
                        $amount5 = $invoice->getAmount();
                    }
                    @endphp
                    <td class="{{ $amount5 ? 'text-right' : 'text-center' }}">{{ formatNo($amount5) }}</td>
                    <td class="text-right">{{ formatNo($customerTotalRow = $amount1 + $amount2 + $amount3 + $amount4 + $amount5) }}</td>
                </tr>
                @php
                    $sum1Amount += $amount1;
                    $sum2Amount += $amount2;
                    $sum3Amount += $amount3;
                    $sum4Amount += $amount4;
                    $sum5Amount += $amount5;
                    $customerTotal += $customerTotalRow;
                @endphp
            @endforeach
            <tr style="height: 50px;border-top: 2px solid #aaa">
                <th colspan="5" class="text-right">{{ trans('app.subtotal') }}</th>
                <th class="text-right">{{ formatNo($sum1Amount) }}</th>
                <th class="text-right">{{ formatNo($sum2Amount) }}</th>
                <th class="text-right">{{ formatNo($sum3Amount) }}</th>
                <th class="text-right">{{ formatNo($sum4Amount) }}</th>
                <th class="text-right">{{ formatNo($sum5Amount) }}</th>
                <th class="text-right">{{ formatNo($customerTotal) }}</th>
            </tr>
                @php
                    $total1Amount += $sum1Amount;
                    $total2Amount += $sum2Amount;
                    $total3Amount += $sum3Amount;
                    $total4Amount += $sum4Amount;
                    $total5Amount += $sum5Amount;
                    $total += $customerTotal;
                @endphp
            @empty
            <tr>
                <td colspan="9">{{ trans('invoice.empty') }}</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" rowspan="2" class="text-right text-middle">{{ trans('app.total') }}</td>
                <td class="text-center">{{ trans('invoice.count') }}</td>
                <td colspan="2" rowspan="2">&nbsp;</td>
                <td class="text-center">1-30 <span class="text-muted">Hari</span></td>
                <td class="text-center">31 - 60 <span class="text-muted">Hari</span></td>
                <td class="text-center">61 - 90 <span class="text-muted">Hari</span></td>
                <td class="text-center">91 - 120 <span class="text-muted">Hari</span></td>
                <td class="text-center">> 120 <span class="text-muted">Hari</span></td>
                <td class="text-center">{{ trans('app.total') }}</td>
            </tr>
            <tr>
                <td class="text-center">{{ $invoices->count() }}</td>
                <td class="text-right">{{ formatNo($total1Amount) }}</td>
                <td class="text-right">{{ formatNo($total2Amount) }}</td>
                <td class="text-right">{{ formatNo($total3Amount) }}</td>
                <td class="text-right">{{ formatNo($total4Amount) }}</td>
                <td class="text-right">{{ formatNo($total5Amount) }}</td>
                <td class="text-right">{{ formatNo($total) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
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
    $('#per_date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true
    });
})();
</script>
@endsection
