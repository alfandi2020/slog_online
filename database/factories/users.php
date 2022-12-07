<?php

use App\Entities\Users\User;

$factory->define(User::class, function (Faker\Generator $faker) {
    $genderId = rand(1, 2);
    $gender = ($genderId == 1) ? 'male' : 'female';

    return [
        'username'       => $faker->username,
        'email'          => $email = $faker->safeEmail,
        'password'       => 'secret',
        'remember_token' => str_random(10),
        'api_token'      => str_random(40),
        'name'           => $faker->name($gender),
        'phone'          => '081234567890',
        'gender_id'      => $genderId,
        'role_id'        => rand(1, 3),
        'network_id'     => 1, // Seeded BAM Kalsel
        'is_active'      => 1,
    ];
});

$factory->state(User::class, 'admin', function (Faker\Generator $faker) {
    return ['role_id' => 1];
});

$factory->state(User::class, 'accounting', function (Faker\Generator $faker) {
    return ['role_id' => 2];
});

$factory->state(User::class, 'sales_counter', function (Faker\Generator $faker) {
    return ['role_id' => 3];
});

$factory->state(User::class, 'warehouse', function (Faker\Generator $faker) {
    return ['role_id' => 4];
});

$factory->state(User::class, 'customer_service', function (Faker\Generator $faker) {
    return ['role_id' => 5];
});

$factory->state(User::class, 'cashier', function (Faker\Generator $faker) {
    return ['role_id' => 6];
});

$factory->state(User::class, 'courier', function (Faker\Generator $faker) {
    return ['role_id' => 7];
});
