<?php

use App\Entities\Customers\Customer;
use App\Entities\Invoices\Cash as CashInvoice;
use App\Entities\Invoices\Cod as CodInvoice;
use App\Entities\Invoices\Invoice;

$factory->define(Invoice::class, function (Faker\Generator $faker) {
    return [
        'number'         => '6300'.date('ym').str_pad(rand(1, 2999), 4, STR_PAD_LEFT),
        'periode'        => str_random(10),
        'date'           => Carbon::now()->format('Y-m-d'),
        'end_date'       => Carbon::now()->addDays(15)->format('Y-m-d'),
        'network_id'     => 1, // Seeded BAM Kalsel
        'customer_id'    => function () {
            return factory(Customer::class)->create(['network_id' => 1])->id;
        },
        'type_id'        => 2, // Credit Invoice
        'creator_id'     => 2, // Seeded Accounting User
        'amount'         => 0,
        'sent_date'      => null,
        'payment_date'   => null,
        'charge_details' => null,
        'notes'          => null,
    ];
});

$factory->state(Invoice::class, 'sent', function (Faker\Generator $faker) {
    return [
        'sent_date' => Carbon::now()->addDay(),
    ];
});

$factory->state(Invoice::class, 'paid', function (Faker\Generator $faker) {
    return [
        'sent_date'    => Carbon::now()->addDay(),
        'payment_date' => Carbon::now()->addDays(10),
    ];
});

$factory->state(Invoice::class, 'verified', function (Faker\Generator $faker) {
    return [
        'sent_date'    => Carbon::now()->addDay(),
        'payment_date' => Carbon::now()->addDays(10),
        'verify_date'  => Carbon::now()->addDays(10),
    ];
});

$factory->define(CashInvoice::class, function (Faker\Generator $faker) {
    return [
        'number'         => 'CSH6300'.date('ym').rand(5000, 7999),
        'periode'        => date('Y-m-d'),
        'date'           => date('Y-m-d'),
        'end_date'       => date('Y-m-d'),
        'network_id'     => 1,
        'customer_id'    => null,
        'type_id'        => 1, // Cash Invoice
        'creator_id'     => 3, // Seeded Sales Counter User
        'amount'         => 0,
        'sent_date'      => null,
        'payment_date'   => null,
        'charge_details' => null,
        'notes'          => null,
    ];
});

$factory->define(CodInvoice::class, function (Faker\Generator $faker) {
    return [
        'number'         => 'COD6300'.date('ym').rand(8000, 9999),
        'periode'        => date('Y-m-d'),
        'date'           => date('Y-m-d'),
        'end_date'       => date('Y-m-d'),
        'network_id'     => 1,
        'customer_id'    => null,
        'type_id'        => 3, // COD Invoice
        'creator_id'     => 5, // Seeded Customer Service User
        'amount'         => 0,
        'sent_date'      => null,
        'payment_date'   => null,
        'charge_details' => null,
        'notes'          => null,
    ];
});
