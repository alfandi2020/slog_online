<table class="full-bordered">
    <tbody>
        <tr>
            <td colspan="2" class="text-center" style="height:69px">
                <div style="margin-bottom:5px;font-size:10px">{{ trans('receipt.number') }}</div>
                <div style="margin-bottom:5px;">{!! $receipt->present()->barcode !!}</div>
                <span class="lead">{{ $receipt->number }}</span><br>
            </td>
        </tr>
        <tr><th colspan="2" class="text-left">3. Data Kiriman</th></tr>
        <tr>
            <td style="height:25px">Ukuran Volume (P x L x T) cm</td>
            <td>Berat Volumetrik</td>
        </tr>
        <tr><td colspan="2">Jenis Barang : {{ strtoupper($receipt->packType->name) }}</td></tr>
        <tr>
            <td>Nilai Barang : {{ formatRp($receipt->pack_value) }}</td>
            <td>Kiriman Diasuransikan : {{ $receipt->isInsured() }}</td>
        </tr>
        <tr>
            <td colspan="2" style="height:30px">
                <p class="strong text-center">Pelaksana Pickup</p>
                <p>Nama :</p>
                <p>NIK :</p>
                <p>Tgl & Waktu :</p>
            </td>
        </tr>
        <tr><th colspan="2" class="text-left">4. Persetujuan Pengirim</th></tr>
        <tr>
            <td colspan="2">
                Dengan ini pengirim menyatakan telah mengisi dengan benar dan memberikan keterangan dengan sebenar-benarnya.
            </td>
        </tr>
        <tr>
            <td style="height:49px;vertical-align:bottom;text-align:center">
                <p>{{ $receipt->pickup_time->format('d-m-Y') }}</p>
                <p>
                    @if ($receipt->customer)
                        {{ $receipt->customer->pic['name'] }}
                    @else
                        {{ $receipt->consignor['name'] }}
                    @endif
                </p>
            </td>
            <td style="vertical-align:bottom;text-align:center">
                Tanda Tangan
            </td>
        </tr>
        <tr><td colspan="2">{{ $key }}/{{ count($receiptDuplicates) }} {{ $value }}</td></tr>
    </tbody>
</table>
