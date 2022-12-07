<?php

return [
    // Labels
    'cash_invoice'    => 'Invoice Tunai',
    'list'            => 'Daftar Invoice Tunai',
    'list_title'      => 'List Invoice Tunai :status (total: :count Invoice)',
    'receipts_count'  => 'Jumlah Resi',
    'search'          => 'Cari Invoice Tunai',
    'search_placeholder' => 'No. Invoice Tunai',
    'search_alert_info' => 'Silakan cari Invoice berdasarkan <b>No. Invoice Tunai</b>',
    'found'           => 'Invoice Tunai ditemukan',
    'not_found'       => 'Invoice Tunai tidak ditemukan',
    'empty'           => 'Belum ada Invoice Tunai',
    'progress'        => 'Progress Invoice Tunai',
    'back_to_index'   => 'Kembali ke daftar Invoice Tunai',
    'print_title'     => 'Cetak Invoice - No. Invoice Tunai :number',
    'to'              => 'Kepada',

    // Actions
    'create'        => 'Buat Invoice Tunai',
    'created'       => 'Invoice Tunai baru berhasil dibuat.',
    'show'          => 'Detail Invoice Tunai',
    'edit'          => 'Edit Invoice',
    'update'        => 'Update Invoice',
    'updated'       => 'Update Invoice Tunai :number telah berhasil.',
    'verify'          => 'Verifikasi Pembayaran',
    'verify_confirm'  => 'Anda yakin akan mengubah status Invoice Tunai :number menjadi \"Closed\"?',
    'verified'        => 'Pembayaran Invoice Tunai :number telah diterima dan status menjadi "Closed".',
    'cannot_verified' => 'Pembayaran Invoice Tunai :number tidak dapat diclosed.',
    'print'           => 'Cetak Invoice Tunai',
    'export_xls'      => 'Export XLS',
    'non_cash_receipt_addition_fails' => 'Hanya Resi dengan pembayaran Tunai yang dapat ditambahkan pada Invice ini.',

    // Sending Invoice
    'send'              => 'Kirim Invoice',
    'send_confirm'      => 'Anda yakin untuk mengubah status Invoice Tunai :number menjadi \"Sent\"?',
    'sent'              => 'Invoice Tunai :number terkirim dan status menjadi "Sent".',
    'unsent'            => 'Invoice Tunai :number tidak dapat dikirim.',
    'take_back'         => 'Tarik Invoice Kembali',
    'take_back_confirm' => 'Anda yakin untuk mengembalikan status Invoice Tunai :number menjadi \"On Proccess\"?',
    'has_taken_back'    => 'Invoice Tunai :number telah ditarik kembali dan status menjadi "On Proccess".',
    'cannot_taken_back' => 'Invoice Tunai :number tidak dapat ditarik kembali.',

    // Attributes
    'number'        => 'No. Invoice Tunai',
    'sent_date'     => 'Tanggal Kirim',
    'payment_date'  => 'Tanggal Pembayaran',
    'verify_date'   => 'Tanggal Closing',
    'amount'        => 'Jumlah Tagihan',

    // Status
    'proccess_status' => 'On Proccess',
    'sent_status'     => 'Sent',
    'closed_status'   => 'Closed',
];