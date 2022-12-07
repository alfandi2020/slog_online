<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Monitor Resi | {{ $queriedYearMonths ?: 'Semua' }}</h3></div>
        <table class="table table-condensed table-bordered table-hover small-text">
            <thead>
                <tr>
                    <th rowspan="2" class="text-middle">Cabang</th>
                    <th colspan="2" class="text-center">Resi Tunai</th>
                    <th colspan="2" class="text-center">Resi Kredit</th>
                    <th colspan="2" class="text-center">Resi COD</th>
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
                    $networkData = $receiptMonitorData['all']->filter(function ($data) use ($network) {
                        return $data->network_id == $network->id;
                    })->first();

                    $network->receipt_total = $networkData ? $networkData->receipt_total : 0;
                    $network->bill_total = $networkData ? $networkData->bill_total : 0;

                    $networkData = $receiptMonitorData['cash']->filter(function ($data) use ($network) {
                        return $data->network_id == $network->id;
                    })->first();

                    $network->cash_receipts_count = $networkData ? $networkData->receipt_total : 0;
                    $network->cash_bill_total = $networkData ? $networkData->bill_total : 0;

                    $networkData = $receiptMonitorData['credit']->filter(function ($data) use ($network) {
                        return $data->network_id == $network->id;
                    })->first();

                    $network->credit_receipts_count = $networkData ? $networkData->receipt_total : 0;
                    $network->credit_bill_total = $networkData ? $networkData->bill_total : 0;

                    $networkData = $receiptMonitorData['cod']->filter(function ($data) use ($network) {
                        return $data->network_id == $network->id;
                    })->first();

                    $network->cod_receipts_count = $networkData ? $networkData->receipt_total : 0;
                    $network->cod_bill_total = $networkData ? $networkData->bill_total : 0;
                ?>
                <tr>
                    <td>{{ $network->name }}</td>
                    <td class="text-center">
                        {{ link_to_route('dashboard.monitor.per-network', $network->cash_receipts_count, [
                            'ym' => $queriedYearMonths,
                            'network_id' => $network->id,
                            'payment_type_id' => 1,
                        ]) }}
                    </td>
                    <td class="text-right">{{ formatRp($network->cash_bill_total) }}</td>
                    <td class="text-center">
                        {{ link_to_route('dashboard.monitor.per-network', $network->credit_receipts_count, [
                            'ym' => $queriedYearMonths,
                            'network_id' => $network->id,
                            'payment_type_id' => 2,
                        ]) }}
                    </td>
                    <td class="text-right">{{ formatRp($network->credit_bill_total) }}</td>
                    <td class="text-center">
                        {{ link_to_route('dashboard.monitor.per-network', $network->cod_receipts_count, [
                            'ym' => $queriedYearMonths,
                            'network_id' => $network->id,
                            'payment_type_id' => 3,
                        ]) }}
                    </td>
                    <td class="text-right">{{ formatRp($network->cod_bill_total) }}</td>
                    <td class="text-center">
                        {{ link_to_route('dashboard.monitor.per-network', $network->receipt_total, [
                            'ym' => $queriedYearMonths,
                            'network_id' => $network->id,
                        ]) }}
                    </td>
                    <td class="text-right">{{ formatRp($network->bill_total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right">Total</th>
                    <th class="text-center">{{ $networks->sum('cash_receipts_count') }}</th>
                    <th class="text-right">{{ formatRp($networks->sum('cash_bill_total')) }}</th>
                    <th class="text-center">{{ $networks->sum('credit_receipts_count') }}</th>
                    <th class="text-right">{{ formatRp($networks->sum('credit_bill_total')) }}</th>
                    <th class="text-center">{{ $networks->sum('cod_receipts_count') }}</th>
                    <th class="text-right">{{ formatRp($networks->sum('cod_bill_total')) }}</th>
                    <th class="text-center">{{ $networks->sum('receipt_total') }}</th>
                    <th class="text-right">{{ formatRp($networks->sum('bill_total')) }}</th>
                </tr>
            </tfoot>
        </table>
</div>