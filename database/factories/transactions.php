<?php

use App\Entities\Transactions\PaymentMethod;
use App\Entities\Transactions\Transaction;
use App\Entities\Users\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(PaymentMethod::class, function (Faker $faker) {

    return [
        'name'        => $faker->name,
        'description' => $faker->sentence,
    ];
});

$factory->define(Transaction::class, function (Faker $faker) {

    return [
        'number'            => date('ym').str_pad(rand(1, 999), 3, STR_PAD_LEFT),
        'invoice_id'        => function () {
            return factory(Invoice::class)->create()->id;
        },
        'date'              => Carbon::now(),
        'in_out'            => 1,
        'amount'            => 10000,
        'creator_id'        => function () {
            return User::find(6); // Seeded cashier
        },
        'handler_id'        => null,
        'verified_at'       => null,
        'payment_method_id' => 1, // Seeded Tunai
        'notes'             => 'Deskripsi transaksi.',
    ];
});
