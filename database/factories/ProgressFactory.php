<?php

use App\Entities\Manifests\Manifest;
use App\Entities\Receipts\Progress;
use App\Entities\Receipts\Receipt;
use Faker\Generator as Faker;

$factory->define(Progress::class, function (Faker $faker) {

    return [
        'receipt_id'          => function () {
            return factory(Receipt::class)->create()->id;
        },
        'manifest_id'         => function () {
            return factory(Manifest::class, 'distribution')->create()->id;
        },
        'creator_id'          => 5, // Seeded cs kalsel
        'start_status'        => 'od', // Seeded cs kalsel
        'creator_location_id' => '6371', // Seeded Kota Banjarmasin
        'handler_id'          => 7, // Seeded courier kalsel
        'handler_location_id' => '6371', // Seeded Kota Banjarmasin
        'end_status'          => 'dl',
        'notes'               => null,
    ];
});
