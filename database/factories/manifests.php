<?php

use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\Problem as ProblemManifest;
use App\Entities\Networks\Network;
use App\Entities\Users\User;

$factory->defineAs(Manifest::class, 'handover', function (Faker\Generator $faker) {

    return [
        'number' => 'M163000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT),
        'type_id' => 1,
        'orig_network_id' => 1, // Kalsel
        'dest_network_id' => 1, // Kalsel
        'weight' => null,
        'pcs_count' => null,
        'creator_id' => 3, // Seeded Sales counter User
        'handler_id' => null,
        'deliver_at' => null,
        'received_at' => null,
        'notes' => null,
    ];
});

$factory->defineAs(Manifest::class, 'delivery', function (Faker\Generator $faker) {

    return [
        'number' => 'M263000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT),
        'type_id' => 2,
        'orig_network_id' => 1, // Kalsel
        'dest_network_id' => function () {
            return factory(Network::class)->states('city')->create();
        },
        'weight' => null,
        'pcs_count' => null,
        'creator_id' => 4, // Seeded Warehouse User
        'handler_id' => null,
        'deliver_at' => null,
        'received_at' => null,
        'notes' => null,
    ];
});

$factory->defineAs(Manifest::class, 'distribution', function (Faker\Generator $faker) {

    return [
        'number' => 'M363000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT),
        'type_id' => 3,
        'orig_network_id' => 1, // Kalsel
        'dest_network_id' => 1, // Kalsel
        'weight' => null,
        'pcs_count' => null,
        'creator_id' => 4, // Seeded Warehouse User
        'handler_id' => 7, // Seeded Courier User
        'deliver_at' => null,
        'received_at' => null,
        'dest_city_id' => '6372', // Seeded Kota Banjarbaru
        'notes' => null,
    ];
});

$factory->defineAs(Manifest::class, 'return', function (Faker\Generator $faker) {

    return [
        'number' => 'M463000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT),
        'type_id' => 4,
        'orig_network_id' => 1, // Kalsel
        'dest_network_id' => function () {
            return factory(Network::class)->states('province')->create();
        },
        'weight' => null,
        'pcs_count' => null,
        'creator_id' => 5, // Seeded CS User
        'handler_id' => null,
        'deliver_at' => null,
        'received_at' => null,
        'notes' => null,
    ];
});

$factory->defineAs(Manifest::class, 'accounting', function (Faker\Generator $faker) {

    return [
        'number' => 'M563000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT),
        'type_id' => 5,
        'orig_network_id' => 1, // Kalsel
        'dest_network_id' => 1, // Kalsel
        'weight' => null,
        'pcs_count' => null,
        'creator_id' => 5, // Seeded CS User
        'handler_id' => null,
        'deliver_at' => null,
        'received_at' => null,
        'notes' => null,
    ];
});

$factory->define(ProblemManifest::class, function (Faker\Generator $faker) {

    return [
        'number' => 'M663000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT),
        'type_id' => 6,
        'orig_network_id' => 1, // Kalsel
        'dest_network_id' => 1, // Kalsel
        'weight' => null,
        'pcs_count' => null,
        'creator_id' => 2, // Seeded Accounting User
        'handler_id' => null,
        'deliver_at' => null,
        'received_at' => null,
        'notes' => null,
    ];
});

$factory->state(Manifest::class, 'sent', function (Faker\Generator $faker) {

    return [
        'deliver_at' => Carbon::now(),
    ];
});

$factory->state(Manifest::class, 'received', function (Faker\Generator $faker) {

    return [
        'deliver_at' => Carbon::now(),
        'received_at' => Carbon::now(),
    ];
});