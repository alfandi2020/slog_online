<?php

use App\Entities\Customers\Customer;
use App\Entities\Networks\DeliveryUnit;
use App\Entities\Services\Pickup;
use Faker\Generator as Faker;

$factory->define(Pickup::class, function (Faker $faker) {

    return [
        'courier_id'       => 7, // Seeded courier kalsel
        'network_id'       => 1, // Seeded BAM kalsel
        'delivery_unit_id' => function () {
            return factory(DeliveryUnit::class)->create()->id;
        },
        'creator_id'       => 4, // Seeded Warehouse kalsel
        'number'           => (new Pickup)->generateNumber(),
        'customers'        => function () {
            return [
                factory(Customer::class)->create()->id => [
                    'receipts_count' => null,
                    'pcs_count'      => null,
                    'items_count'    => null,
                    'weight_total'   => null,
                    'notes'          => null,
                ],
            ];
        },
    ];
});
