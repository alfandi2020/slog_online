<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('receipt.print_title', ['number' => $receipt->number]) }}</title>
    {!! Html::style('css/pdf.css') !!}
</head>
<body>
    @foreach ($receiptDuplicates as $key => $value)
    <?php
        $showAble = false;
        if (in_array($key, [1, 2, 3]) && in_array($receipt->payment_type_id, [1, 3])) {
            $showAble = true;
        } elseif (in_array($key, [3]) && $receipt->payment_type_id == 2) {
            $showAble = true;
        } elseif (in_array($key, [1, 2, 3, 4, 5]) && $receipt->payment_type_id == 3) {
            $showAble = true;
        }

    ?>
    <div>
        <table class="receipt-table">
            <tbody>
                <tr>
                    <td style="width:200px;">
                        {!! Html::image(url('imgs/logo_new.png'), 'logo', ['style' => 'width: 180px;']) !!}
                    </td>
                    <td style="width:368px">
                        <table style="font-size: 10px;">
                            <tbody>
                                <tr>
                                    <td style="width:355px; height:70px;">
                                        <div style="height:12px; font-size: 11px; text-align: center; margin-top: 2px;">SINERGI LOGISTIK</div>
                                        <div style="height:12px; font-size: 10px; text-align: center;">CARGO & LOGISTICS SERVICES</div>
                                        <div style="height:11px; font-size: 9px; text-align: center;">KESELURUH INDONESIA (UDARA, DARAT, LAUT)</div>
                                        <div style="height:10px; font-size: 8px; text-align: center;">Jl. Swadaya Rw. Binong No 90, Bambu Apus Cipayung, Jakarta Timur, Telp. 021-85525360</div>
                                        <div style="height:10px; font-size: 8px; text-align: center;">Email: s.log@sinergilogistik.com | Website : www.sinergilogistik.com</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width:200px;">
                        <div style="text-align:right;">
                            {!! generateQrCode($receipt->number, 65) !!}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="receipt-table">
            <tbody>
                <tr>
                    <td style="width:238px">
                        <table class="full-bordered" style="font-size: 8px;">
                            <tbody>
                                <tr>
                                    <td style="width:112px; height: 9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">No. Resi</div>
                                    </td>
                                    <td style="width:112px; height: 9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Pelanggan</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 9px;">
                                        <div style="text-align: center;">{{ $receipt->number }}</div>
                                    </td>
                                    <td style="height: 9px;">
                                        <div style="text-align: center;">{{ substr($receipt->consignor['name'], 0, 20) }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"; style="height:60px;">
                                        <div style="text-align: left;">Pengirim :</div>
                                        <div class="strong" >{{ $receipt->consignor['name'] }}</div>
                                        <p>
                                            {{ $receipt->consignor['address'][1] }}<br>
                                            {{ $receipt->consignor['address'][2] }}<br>
                                            {{ $receipt->consignor['address'][3] }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Telp. Pengirim</div>
                                    </td>
                                    <td style="height: 9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Kode Pos</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 9px;">
                                        <div style="text-align: center;">{{ $receipt->consignor['phone'] }}</div>
                                    </td>
                                    <td style="height: 9px;">
                                        <div style="text-align: center;">{{ $receipt->consignor['postal_code'] }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:60px;">
                                        <div style="text-align: center;">Tanda Tangan Pengirim</div>
                                    </td>
                                    <td style="height:60px;text-align: center;">
                                        <div>Tanda Tangan Petugas</div>
                                        <div style="margin-top: 40px">{{ $receipt->pickup_time->format('d-m-Y') }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"; style="height:60px;">
                                        <div style="text-align: center;">Tanda Tangan Penerima</div>
                                        <br><br><br><br>
                                        <div style="text-align: center;">Nama Jelas & Cap</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width:240px">
                        <table class="full-bordered" style="font-size: 8px;">
                            <tbody>
                                <tr>
                                    <td style="width:80px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Asal</div>
                                    </td>
                                    <td style="width:80px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Tujuan</div>
                                    </td>
                                    <td style="width:40px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Koli</div>
                                    </td>
                                    <td style="width:40px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center;  color: #fff; font-size: 10px;">Dus</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:9px;">
                                        <div style="text-align: center;">{{ substr(str_replace(['Kota ', 'Kab. ', 'Kec. '], '', $receipt->originName()), 0, 20) }}</div>
                                    </td>
                                    <td style="height:9px;">
                                        <div style="text-align: center;">{{ substr(str_replace(['Kota ', 'Kab. ', 'Kec. '], '', $receipt->destinationName()), 0, 20) }}</div>
                                    </td>
                                    <td style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->pcs_count }}</div>
                                    </td>
                                    <td style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->items_count }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"; style="height:60px;">
                                        <div style="text-align: left;">Penerima :</div>
                                        <div class="strong">{{ $receipt->consignee['name'] }}</div>
                                        <p>
                                            {{ $receipt->consignee['address'][1] }}<br>
                                            {{ $receipt->consignee['address'][2] }}<br>
                                            {{ $receipt->consignee['address'][3] }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"; style="height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Telp. Penerima</div>
                                    </td>
                                    <td colspan="2"; style="height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Kode Pos</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->consignee['phone'] }}</div>
                                    </td>
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->consignee['postal_code'] }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"; style="height:27px;">
                                        <div style="text-align: left;">No. DO/Faktur :</div>
                                        <div>{{ $receipt->customer_invoice_no }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"; style="height:43px;">
                                        <div style="text-align: left;">Remarks :</div>
                                        <div>{{ $receipt->notes }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"; style="height:25px; padding: 12px">
                                        <div class="strong" style="text-align: center;">Pengirim menyatakan bahwa semua informasi pada Surat Tanda Terima Barang adalah BENAR</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width:240px">
                        <table class="full-bordered" style="font-size: 8px;">
                            <tbody>
                                <tr>
                                    <td colspan="4"; style="width:60px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Berat</div>
                                    </td>
                                    <td colspan="10"; style="width:90px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Volume</div>
                                    </td>
                                    <td colspan="10"; style="width:90px; height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Harga Kilo / Koli</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"; style="height:9px;">
                                        <div style="text-align: center;">{{ str_replace('.00', '', $receipt->weight) }} Kg</div>
                                    </td>
                                    <td colspan="10"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td>
                                    <td colspan="10"; style="height:9px;">
                                        <div style="text-align: center;">{{ formatRp($receipt->base_rate) }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="24"; style="height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff;">Cara Pembayaran</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3"; colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td>
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->payment_type_id == 1 ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="21"; style="height:9px;">
                                        <div style="text-align: left;">Tunai</div>
                                    </td>
                                </tr>
                                <tr>
                                    {{-- <td style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td> --}}
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->payment_type_id == 3 ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="21"; style="height:9px;">
                                        <div style="text-align: left;">Bayar Tujuan</div>
                                    </td>
                                </tr>
                                <tr>
                                    {{-- <td style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td> --}}
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->payment_type_id == 2 ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="21"; style="height:9px;">
                                        <div style="text-align: left;">Kredit</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="24"; style="height:9px; background-color: #009000;">
                                        <div class="strong" style="text-align: center; color: #fff; font-size: 10px;">Jenis Barang</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="24"; style="height:9px;">
                                        <div style="text-align: center;">{{ strtoupper($receipt->packType->name) }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="12"; style="height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff;">Jenis Kiriman</div>
                                    </td>
                                    <td colspan="12"; style="height:9px; background-color: #009000">
                                        <div class="strong" style="text-align: center; color: #fff;">Service</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3" colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td>
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->packType->name == 'Dokumen' ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="9"; style="height:9px;">
                                        <div style="text-align: left;">Dokumen</div>
                                    </td>
                                    <td rowspan="3" colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td>
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->service_id == '11' ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="9"; style="height:9px;">
                                        <div style="text-align: left;">Express</div>
                                    </td>
                                </tr>
                                <tr>
                                    {{-- <td rowspan="3" colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td> --}}
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->packType->name == 'Paket' ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="9"; style="height:9px;">
                                        <div style="text-align: left;">Paket</div>
                                    </td>
                                    {{-- <td rowspan="3" colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td> --}}
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->service_id == '21' ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="9"; style="height:9px;">
                                        <div style="text-align: left;">Reguler</div>
                                    </td>
                                </tr>
                                <tr>
                                    {{-- <td rowspan="3" colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td> --}}
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ !in_array($receipt->packType->name, ['Paket', 'Dokumen']) ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="9"; style="height:9px;">
                                        <div style="text-align: left;">Lain-lain</div>
                                    </td>
                                    {{-- <td rowspan="3" colspan="1"; style="height:9px;">
                                        <div style="text-align: center;"></div>
                                    </td> --}}
                                    <td colspan="2"; style="height:9px;">
                                        <div style="text-align: center;">{{ $receipt->service_id == '41' ? 'X' : '' }}</div>
                                    </td>
                                    <td colspan="9"; style="height:9px;">
                                        <div style="text-align: left;">Trucking</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">Biaya</div>
                                    </td>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">{{ $showAble ? formatRp($baseCost = $receipt->costs_detail['base_charge']) : '' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">Biaya Packing</div>
                                    </td>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">{{ $showAble ? formatRp($receipt->costs_detail['packing_cost']) : '' }}</div>
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">Biaya Asuransi</div>
                                    </td>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">{{ $showAble ? formatRp($receipt->costs_detail['insurance_cost']) : '' }}</div>
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">Biaya Tambahan</div>
                                    </td>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">{{ $showAble ? formatRp($receipt->costs_detail['add_cost']) : '' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">Total</div>
                                    </td>
                                    <td colspan="12"; style="height:9px;">
                                        <div style="text-align: right;">{{ $showAble ? formatRp($receipt->amount) : '' }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @if ($key != count($receiptDuplicates))
    <hr style="border-bottom-style: dashed; border-top: none;margin: 2px 0 17px 0;">
    {{-- <div class="page-break"></div> --}}
    @endif
    @endforeach
</body>
</html>
