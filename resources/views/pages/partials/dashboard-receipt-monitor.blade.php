@php
    $tab = request('tab');
@endphp
<div class="panel panel-default table-responsive">
    <div class="panel-heading"><h3 class="panel-title">{{ trans('report.monitor.'.$tab) }} | {{ $queriedYearMonths ?: 'Semua' }}</h3></div>
    <table class="table table-condensed table-bordered table-hover small-text">
        <thead>
            <tr>
                <th rowspan="2" class="text-middle">Cabang</th>
                <th colspan="2" class="text-center">Belum Invoice</th>
                <th colspan="2" class="text-center">Terinvoice</th>
                <th colspan="2" class="text-center">Lunas</th>
                <th rowspan="2" class="text-center text-middle">Total Resi</th>
                <th rowspan="2" class="text-center text-middle">Total Rupiah</th>
            </tr>
            <tr>
                <td class="text-center">Jumlah</td>
                <td class="text-center">Rupiah</td>
                <td class="text-center">Jumlah</td>
                <td class="text-center">Rupiah</td>
                <td class="text-center">Jumlah</td>
                <td class="text-center">Rupiah</td>
            </tr>
        </thead>
        <tbody>
            @foreach($networks as $network)
            <?php
            $networkData = $receiptPaymentMonitorData['all']->filter(function ($data) use ($network) {
                return $data->network_id == $network->id;
            })->first();

            $network->receipt_total = $networkData ? $networkData->receipt_total : 0;
            $network->bill_total = $networkData ? $networkData->bill_total : 0;

            $networkData = $receiptPaymentMonitorData['uninvoiced']->filter(function ($data) use ($network) {
                return $data->network_id == $network->id;
            })->first();

            $network->uninvoiced_receipts_count = $networkData ? $networkData->receipt_total : 0;
            $network->uninvoiced_bill_total = $networkData ? $networkData->bill_total : 0;

            $networkData = $receiptPaymentMonitorData['invoiced']->filter(function ($data) use ($network) {
                return $data->network_id == $network->id;
            })->first();

            $network->invoiced_receipts_count = $networkData ? $networkData->receipt_total : 0;
            $network->invoiced_bill_total = $networkData ? $networkData->bill_total : 0;

            $networkData = $receiptPaymentMonitorData['paid']->filter(function ($data) use ($network) {
                return $data->network_id == $network->id;
            })->first();

            $network->paid_receipts_count = $networkData ? $networkData->receipt_total : 0;
            $network->paid_bill_total = $networkData ? $networkData->bill_total : 0;
            ?>
            <tr>
                <td>{{ $network->name }}</td>
                <td class="text-center">
                    {{ link_to_route('dashboard.monitor.uninvoiced', $network->uninvoiced_receipts_count, [
                        'ym' => $queriedYearMonths,
                        'network_id' => $network->id,
                        'payment_type_id' => $paymentTypes->search($tab),
                    ]) }}
                </td>
                <td class="text-right">{{ formatRp($network->uninvoiced_bill_total) }}</td>
                <td class="text-center">
                    {{ link_to_route('dashboard.monitor.invoiced', $network->invoiced_receipts_count, [
                        'ym' => $queriedYearMonths,
                        'network_id' => $network->id,
                        'payment_type_id' => $paymentTypes->search($tab),
                    ]) }}
                </td>
                <td class="text-right">{{ formatRp($network->invoiced_bill_total) }}</td>
                <td class="text-center">
                    {{ link_to_route('dashboard.monitor.paid', $network->paid_receipts_count, [
                        'ym' => $queriedYearMonths,
                        'network_id' => $network->id,
                        'payment_type_id' => $paymentTypes->search($tab),
                    ]) }}
                </td>
                <td class="text-right">{{ formatRp($network->paid_bill_total) }}</td>
                <td class="text-center">
                    {{ link_to_route('dashboard.monitor.per-network', $network->receipt_total, [
                        'ym' => $queriedYearMonths,
                        'network_id' => $network->id,
                        'payment_type_id' => $paymentTypes->search($tab),
                    ]) }}
                </td>
                <td class="text-right">{{ formatRp($network->bill_total) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right">Total</th>
                <th class="text-center">{{ $networks->sum('uninvoiced_receipts_count') }}</th>
                <th class="text-right">{{ formatRp($networks->sum('uninvoiced_bill_total')) }}</th>
                <th class="text-center">{{ $networks->sum('invoiced_receipts_count') }}</th>
                <th class="text-right">{{ formatRp($networks->sum('invoiced_bill_total')) }}</th>
                <th class="text-center">{{ $networks->sum('paid_receipts_count') }}</th>
                <th class="text-right">{{ formatRp($networks->sum('paid_bill_total')) }}</th>
                <th class="text-center">{{ $networks->sum('receipt_total') }}</th>
                <th class="text-right">{{ formatRp($networks->sum('bill_total')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>