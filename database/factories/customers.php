<?php

use App\Entities\Customers\Customer;

$factory->define(Customer::class, function (Faker\Generator $faker) {

    return [
        'comodity_id' => 3, // Seeded Expedisi
        'network_id'  => 1, // Seeded BAM Kalsel
        'account_no'  => rand(1000, 9999),
        'code'        => rand(1000, 9999),
        'name'        => $faker->company,
        'npwp'        => null,
        'is_taxed'    => 0,
        'pic'         => [
            'name'  => $faker->name,
            'phone' => '081234567890',
            'email' => $faker->safeEmail,
        ],
        'start_date'  => '2017-01-01',
        'address'     => [
            1 => $faker->address,
            2 => $faker->city,
            3 => $faker->state,
        ],
        'category_id' => 1, // Available category: 1, 2, and 3
    ];
});
