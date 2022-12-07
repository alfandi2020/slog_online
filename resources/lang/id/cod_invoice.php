<?php

return [
    // Labels
    'cod_invoice'        => 'Invoice COD',
    'list'               => 'Daftar Invoice COD',
    'list_title'         => 'List Invoice COD :status (total: :count Invoice)',
    'receipts_count'     => 'Jumlah Resi',
    'search'             => 'Cari Invoice COD',
    'search_placeholder' => 'No. Invoice COD',
    'search_alert_info'  => 'Silakan cari Invoice berdasarkan <b>No. Invoice COD</b>',
    'found'              => 'Invoice COD ditemukan',
    'not_found'          => 'Invoice COD tidak ditemukan',
    'empty'              => 'Belum ada Invoice COD',
    'progress'           => 'Progress Invoice COD',
    'back_to_index'      => 'Kembali ke daftar Invoice COD',
    'print_title'        => 'Cetak Invoice - No. Invoice COD :number',
    'to'                 => 'Kepada',

    // Actions
    'create'          => 'Buat Invoice COD',
    'created'         => 'Invoice COD baru berhasil dibuat.',
    'show'            => 'Detail Invoice COD',
    'edit'            => 'Edit Invoice',
    'update'          => 'Update Invoice',
    'updated'         => 'Update Invoice COD :number telah berhasil.',
    'verify'          => 'Verifikasi Pembayaran',
    'verify_confirm'  => 'Anda yakin akan mengubah status Invoice COD :number menjadi \"Closed\"?',
    'verified'        => 'Pembayaran Invoice COD :number telah diterima dan status menjadi "Closed".',
    'cannot_verified' => 'Pembayaran Invoice COD :number tidak dapat diclosed.',
    'print'           => 'Cetak Invoice COD',
    'export_xls'      => 'Export XLS',

    // COD Receipts
    'non_cod_receipt_addition_fails' => 'Hanya Resi dengan pembayaran COD yang dapat ditambahkan pada Invoice ini.',
    'dl_bd_receipt_addition_fails'   => 'Silakan Input sebagai Resi Kembali dulu.',

    // Sending Invoice
    'send'              => 'Kirim Invoice',
    'send_confirm'      => 'Anda yakin untuk mengubah status Invoice COD :number menjadi \"Sent\"?',
    'sent'              => 'Invoice COD :number terkirim dan status menjadi "Sent".',
    'unsent'            => 'Invoice COD :number tidak dapat dikirim.',
    'take_back'         => 'Tarik Invoice Kembali',
    'take_back_confirm' => 'Anda yakin untuk mengembalikan status Invoice COD :number menjadi \"On Proccess\"?',
    'has_taken_back'    => 'Invoice COD :number telah ditarik kembali dan status menjadi "On Proccess".',
    'cannot_taken_back' => 'Invoice COD :number tidak dapat ditarik kembali.',

    // Attributes
    'number'       => 'No. Invoice COD',
    'sent_date'    => 'Tanggal Kirim',
    'payment_date' => 'Tanggal Pembayaran',
    'verify_date'  => 'Tanggal Closing',
    'amount'       => 'Jumlah Tagihan',

    // Status
    'proccess_status' => 'On Proccess',
    'sent_status'     => 'Sent',
    'closed_status'   => 'Closed',
];
