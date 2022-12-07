<?php

return [
    // Labels
    'invoice'            => 'Invoice',
    'list'               => 'Daftar Invoice',
    'receipts_count'     => 'Jumlah Resi',
    'search'             => 'Cari Invoice',
    'search_placeholder' => 'No. Invoice / Customer',
    'search_alert_info'  => 'Silakan cari Invoice berdasarkan <b>No. Invoice</b>, <b>No. Customer</b>, atau <b>Nama Customer</b>',
    'found'              => 'Invoice ditemukan',
    'not_found'          => 'Invoice tidak ditemukan',
    'empty'              => 'Belum ada Invoice',
    'progress'           => 'Progress Invoice',
    'back_to_index'      => 'Kembali ke daftar Invoice',
    'print_title'        => 'Cetak Invoice - No. Invoice :number',
    'to'                 => 'Kepada',
    'count'              => 'Jumlah Invoice',
    'calculation'        => 'Kalkulasi Invoice',
    'base_tax'           => 'Dasar Pengenaan Pajak',
    'ppn'                => 'PPn',

    // Actions
    'create'              => 'Buat Invoice',
    'create_for_customer' => 'Buat Invoice Customer',
    'created'             => 'Invoice baru berhasil dibuat.',
    'show'                => 'Detail Invoice',
    'edit'                => 'Edit Invoice',
    'update'              => 'Update Invoice',
    'updated'             => 'Update Invoice :number telah berhasil.',
    'delete'              => 'Hapus Invoice',
    'deleted'             => 'Invoice :number berhasil dihapus.',
    'undeleted'           => 'Invoice :number gagal dihapus.',
    'payment'             => 'Pembayaran Invoice',
    'payment_confirm'     => 'Anda yakin akan mengubah status Invoice :number menjadi PAID pada tanggal Pembayaran tersebut?',
    'payment_entry'       => 'Entry Pembayaran',
    'outcome_entry'       => 'Entry Pengeluaran',
    'empty_payment'       => 'Belum ada pembayaran.',
    'payment_added'       => 'Pembayaran invoice :number telah ditambahkan.',
    'payment_update'      => 'Update Pembayaran',
    'payment_updated'     => 'Update Pembayaran :number berhasil.',
    'paid'                => 'Invoice :number terbayar dan status menjadi "Paid".',
    'verify'              => 'Verifikasi Pembayaran',
    'verify_confirm'      => 'Anda yakin akan mengubah status Invoice :number menjadi CLOSED?',
    'verified'            => 'Pembayaran Invoice :number telah diterima dan status menjadi CLOSED.',
    'cannot_verified'     => 'Pembayaran Invoice :number tidak dapat diclosed.',
    'print'               => 'Cetak Invoice',
    'export_xls'          => 'Export XLS',
    'add_remove_receipt'  => 'Input/Hapus Resi',

    'non_credit_receipt_addition_fails' => 'Hanya Resi dengan pembayaran Kredit yang dapat ditambahkan pada Invice ini.',
    'receipts_bill_amount'              => 'Total Tagihan Resi',
    'outcome_payment_added'             => 'Pembayaran pengeluaran invoice :number telah ditambahkan.',

    // Sending Invoice
    'send'              => 'Kirim Invoice',
    'send_confirm'      => 'Anda yakin untuk mengubah status Invoice :number menjadi \"Sent\"?',
    'sent'              => 'Invoice :number terkirim dan status menjadi "Sent".',
    'unsent'            => 'Invoice :number tidak dapat dikirim.',
    'set_paid'          => 'Set Lunas',
    'set_unpaid'        => 'Set Belum Lunas',
    'paid_confirm'      => 'Anda yakin untuk MELUNASKAN status Invoice :number ini?',
    'paid'              => 'Invoice :number telah lunas.',
    'unpaid'            => 'Invoice :number belum lunas.',
    'take_back'         => 'Tarik Invoice Kembali',
    'take_back_confirm' => 'Anda yakin untuk mengembalikan status Invoice :number menjadi \"On Proccess\"?',
    'has_taken_back'    => 'Invoice :number telah ditarik kembali dan status menjadi "On Proccess".',
    'cannot_taken_back' => 'Invoice :number tidak dapat ditarik kembali.',
    'set_problem'       => 'Set Status Macet',
    'problem_confirm'   => 'Anda yakin akan mengubah status menjadi Invoice Macet?',
    'problem'           => 'Invoice :number berstatus Macet.',
    'unset_problem'     => 'Batal Status Macet',

    // Attributes
    'number'         => 'No. Invoice',
    'date'           => 'Tanggal Invoice',
    'end_date'       => 'Jatuh Tempo',
    'created_date'   => 'Tanggal Dibuat',
    'sent_date'      => 'Tanggal Kirim',
    'payment_date'   => 'Tanggal Pelunasan',
    'verify_date'    => 'Tanggal Closing',
    'connote_count'  => 'Jumlah Connote',
    'total'          => 'Jumlah Tagihan',
    'periode'        => 'Periode',
    'amount'         => 'Jumlah Tagihan',
    'customer'       => 'Customer',
    'agent'          => 'Agen',
    'discount'       => 'Diskon',
    'admin_fee'      => 'Biaya Administrasi',
    'creator'        => 'Yang membuat',
    'handler'        => 'Verifikator',
    'payment_method' => 'Cara Pembayaran',
    'payment_amount' => 'Jumlah Dibayar',

    // Relations
    'payments' => 'List Pembayaran',
    'receipts' => 'List Resi',

    // Payment Types
    'payment_type'  => 'Jenis Pembayaran',
    'payment_types' => [
        'repayment'   => 'Pelunasan',
        'installment' => 'Cicilan',
    ],

    // Delivery to customer
    'delivery_info'    => 'Pengiriman Invoice',
    'update_delivery'  => 'Update Pengiriman',
    'delivery_updated' => 'Update pengiriman invoice :number berhasil.',
    'consignor'        => 'Yang mengirimkan',
    'consignee'        => 'Yang menerima',
    'received_date'    => 'Tanggal Diterima',
];
