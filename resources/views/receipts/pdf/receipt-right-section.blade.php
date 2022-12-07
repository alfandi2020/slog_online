<table class="full-bordered">
    <tbody>
        <tr>
            <td class="text-center strong">{{ trans('receipt.origin') }}</td>
            <td class="text-center strong">{{ trans('receipt.destination') }}</td>
        </tr>
        <tr>
            <td class="text-center">{{ $receipt->originName() }}</td>
            <td class="text-center">{{ $receipt->destinationName() }}</td>
        </tr>
        <tr>
            <td class="text-center strong">{{ trans('receipt.pcs_count') }} / {{ trans('receipt.items_count') }}</td>
            <td class="text-center strong">{{ trans('receipt.weight') }}</td>
        </tr>
        <tr>
            <td class="text-center">{{ $receipt->pcs_count }} / {{ $receipt->items_count }}</td>
            <td class="text-center">{{ $receipt->weight }} Kg</td>
        </tr>
        <tr><th colspan="2" class="text-left">5. {{ trans('service.service') }}</th></tr>
        <tr>
            <td colspan="2" style="height:25px;">{{ $receipt->service() }}</td>
            {{-- <td style="height:25px; border-right: none;">{{ $receipt->service() }}</td> --}}
            {{-- <td style="text-align: right; border-left: none;"><img src="{{ asset('imgs/asperindo.jpg') }}" style="height: 20px"></td> --}}
        </tr>
        <tr>
            <th colspan="2" class="text-left">
                6. {{ trans('receipt.payment') }} ({{ $receipt->present()->paymentType() }})
            </th>
        </tr>
        <tr>
            <td style="height:auto">{{ trans('receipt.base_charge') }}</td>
            <td style="height:auto" class="text-right">{{ $showAble ? formatRp($baseCost = $receipt->costs_detail['base_charge']) : '' }}</td>
        </tr>
        <tr>
            <td style="height:auto">{{ trans('receipt.discount') }}</td>
            <td style="height:auto" class="text-right">{{ $showAble ? formatDiscountRp($receipt->costs_detail['discount']) : '' }}</td>
        </tr>
        <tr>
            <td style="height:auto">{{ trans('receipt.subtotal') }}</td>
            <td style="height:auto" class="text-right">{{ $showAble ? formatRp($receipt->costs_detail['subtotal']) : '' }}</td>
        </tr>
        <tr>
            <td style="height:auto">{{ trans('receipt.packing_cost') }}</td>
            <td style="height:auto" class="text-right">{{ $showAble ? formatRp($receipt->costs_detail['packing_cost']) : '' }}</td>
        </tr>
        <tr>
            <td style="height:auto">{{ trans('receipt.insurance_cost') }}</td>
            <td style="height:auto" class="text-right">{{ $showAble ? formatRp($receipt->costs_detail['insurance_cost']) : '' }}</td>
        </tr>
        <tr>
            <td style="height:auto">{{ trans('receipt.admin_fee') }}</td>
            <td style="height:auto" class="text-right">{{ $showAble ? formatRp($receipt->costs_detail['admin_fee']) : '' }}</td>
        </tr>
        <tr>
            <td style="height:13px" class="strong">{{ trans('receipt.amount') }}</td>
            <td class="text-right strong"><strong>{{ $showAble ? formatRp($receipt->amount) : '' }}</strong></td>
        </tr>
        <tr>
            <td colspan="2" style="height:60px">
                <p class="strong">Terkirim</p>
                <p>Diterima dalam keadaan baik.</p>
                <p>Nama : </p>
                <p>No. Telp :</p>
                <p>
                    Tgl & Waktu :
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Tanda Tangan
                </p>
            </td>
        </tr>
    </tbody>
</table>