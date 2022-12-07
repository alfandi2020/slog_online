<?php

use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Proof;
use App\Entities\Receipts\Receipt;
use Faker\Generator as Faker;

$factory->define(Proof::class, function (Faker $faker) {

    return [
        'progress_id'  => 1, // fake progress_id
        'receipt_id'   => function () {
            return factory(Receipt::class)->create()->id;
        },
        'manifest_id'  => function () {
            return factory(Manifest::class, 'distribution')->create()->id;
        },
        'courier_id'   => 7, // Seeded courier kalsel
        'creator_id'   => 5, // Seeded cs kalsel
        'location_id'  => '6371', // Seeded Kota Banjarmasin
        'status_code'  => 'dl',
        'recipient'    => 'Nama Penerima',
        'delivered_at' => Carbon::now(),
        'notes'        => null,
    ];
});
