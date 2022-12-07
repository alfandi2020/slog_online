<div class="panel panel-default table-responsive">
    <div class="panel-heading"><h3 class="panel-title">Resi Tertahan | {{ $queriedYearMonths ?: 'Semua' }}</h3></div>
    <table class="table table-condensed table-bordered table-hover small-text">
        <thead>
            <tr>
                <th rowspan="2" class="text-middle">User</th>
                @foreach($deliveryStatuses as $statusCode => $statusName)
                <th colspan="2" class="text-center">{{ $statusName }}</th>
                @endforeach
                <th rowspan="2" class="text-center text-middle">Total Resi</th>
                <th rowspan="2" class="text-center text-middle">Total Rupiah</th>
            </tr>
            <tr>
                @foreach($deliveryStatuses as $statusCode => $statusName)
                <td class="text-center">Jumlah</td>
                <td class="text-center">Rupiah</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <?php
                $userReceipts = $retainedReceiptsData->filter(function ($receipt) use ($user) {
                    return $receipt->lastOfficerId() == $user->id;
                });

                $user->receipt_total = $userReceipts ? $userReceipts->count() : 0;
                $user->bill_total = $userReceipts ? $userReceipts->sum('bill_amount') : 0;
            ?>
            <tr>
                <td>
                    {{ $user->name }}
                    <div class="text-warning">{{ $user->present()->networkName }}</div>
                </td>
                @foreach($deliveryStatuses as $statusCode => $statusName)
                <?php
                $specifyReceipts = $userReceipts->filter(function ($receipt) use ($statusCode) {
                    return $receipt->status_code == $statusCode;
                });
                $user->{$statusCode.'_receipts_count'} = $specifyReceipts ? $specifyReceipts->count() : 0;
                $user->{$statusCode.'_bill_total'} = $specifyReceipts ? $specifyReceipts->sum('bill_amount') : 0;
                ?>
                <td class="text-center text-middle">
                    {{ link_to_route('reports.receipt.retained', $user->{$statusCode.'_receipts_count'}, [
                        'date' => $queriedYearMonths,
                        'officer_id' => $user->id,
                        'status_code' => $statusCode,
                    ]) }}
                </td>
                <td class="text-right text-middle">{{ formatRp($user->{$statusCode.'_bill_total'}) }}</td>
                @endforeach
                <td class="text-center text-middle">
                    {{ link_to_route('reports.receipt.retained', $user->receipt_total, [
                        'date' => $queriedYearMonths,
                        'officer_id' => $user->id,
                    ]) }}
                </td>
                <td class="text-right text-middle">{{ formatRp($user->bill_total) }}</td>
            </tr>
            <?php

            ?>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right">Total</th>
                @foreach($deliveryStatuses as $statusCode => $statusName)
                <th class="text-center">{{ $users->sum($statusCode.'_receipts_count') }}</th>
                <th class="text-right">{{ formatRp($users->sum($statusCode.'_bill_total')) }}</th>
                @endforeach
                <th class="text-center">{{ $users->sum('receipt_total') }}</th>
                <th class="text-right">{{ formatRp($users->sum('bill_total')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>