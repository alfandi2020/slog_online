<?php

use App\Entities\Customers\Customer;
use App\Entities\Receipts\Receipt;
use App\Entities\Services\Rate;
use App\Entities\Services\Service;
use Carbon\Carbon;

$factory->define(Receipt::class, function (Faker\Generator $faker) {

    $faker->addProvider(new Faker\Provider\id_ID\Person($faker));
    $faker->addProvider(new Faker\Provider\id_ID\Address($faker));
    $faker->addProvider(new Faker\Provider\id_ID\PhoneNumber($faker));

    $serviceId = collect(Service::only([11, 12, 13, 14, 21]))->keys()->random();
    $rate = factory(Rate::class)->create(['rate_kg' => 1000]);

    $receiptNumber = '63000000'.date('ym').str_pad(rand(1, 9999), 6, STR_PAD_LEFT);

    return [
        'service_id'       => $serviceId,
        'number'           => $receiptNumber,
        'pickup_time'      => Carbon::now(),
        'items_detail'     => null,
        'pcs_count'        => 1,
        'items_count'      => 1,
        'weight'           => 1,
        'pack_content'     => null,
        'pack_value'       => null,
        'orig_city_id'     => $rate->orig_city_id,
        'orig_district_id' => $rate->orig_district_id,
        'dest_city_id'     => $rate->dest_city_id,
        'dest_district_id' => $rate->dest_district_id,
        'charged_on'       => 1,
        'consignor'        => [
            'name'        => $faker->name,
            'address'     => [1 => $faker->streetAddress, $faker->city, $faker->state],
            'phone'       => $faker->phoneNumber,
            'postal_code' => $faker->postcode,
        ],
        'consignee'        => [
            'name'        => $faker->name,
            'address'     => [1 => $faker->streetAddress, $faker->city, $faker->state],
            'phone'       => $faker->phoneNumber,
            'postal_code' => $faker->postcode,
        ],
        'creator_id'       => 3, // Seeded Sales counter
        'network_id'       => 1, // Kalsel
        'status_code'      => 'de',
        'invoice_id'       => null,
        'rate_id'          => $rate->id,
        'amount'           => $rate->rate_kg,
        'bill_amount'      => $rate->rate_kg,
        'base_rate'        => $rate->rate_kg,
        'reference_no'     => null,
        'payment_type_id'  => 1,
        'customer_id'      => null,
        'pack_type_id'     => 1,
        'costs_detail'     => [
            "base_charge"    => $rate->rate_kg,
            "discount"       => 0,
            "subtotal"       => $rate->rate_kg,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $rate->rate_kg,
        ],
        'notes'            => null,
        'deleted_at'       => null,
    ];
});

$factory->defineAs(Receipt::class, 'charge_on_pc', function (Faker\Generator $faker) {

    $faker->addProvider(new Faker\Provider\id_ID\Person($faker));
    $faker->addProvider(new Faker\Provider\id_ID\Address($faker));
    $faker->addProvider(new Faker\Provider\id_ID\PhoneNumber($faker));

    $serviceId = collect(Service::only([11, 12, 13, 14, 21]))->keys()->random();
    $rate = factory(Rate::class)->create(['rate_pc' => 1000]);

    $receiptNumber = '63000000'.date('ym').str_pad(rand(1, 9999), 6, STR_PAD_LEFT);

    return [
        'service_id'       => $serviceId,
        'number'           => $receiptNumber,
        'pickup_time'      => Carbon::now(),
        'items_detail'     => null,
        'pcs_count'        => 1,
        'items_count'      => 1,
        'weight'           => 1,
        'pack_content'     => null,
        'pack_value'       => null,
        'orig_city_id'     => $rate->orig_city_id,
        'orig_district_id' => $rate->orig_district_id,
        'dest_city_id'     => $rate->dest_city_id,
        'dest_district_id' => $rate->dest_district_id,
        'charged_on'       => 2,
        'consignor'        => [
            'name'        => $faker->name,
            'address'     => [1 => $faker->streetAddress, $faker->city, $faker->state],
            'phone'       => $faker->phoneNumber,
            'postal_code' => $faker->postcode,
        ],
        'consignee'        => [
            'name'        => $faker->name,
            'address'     => [1 => $faker->streetAddress, $faker->city, $faker->state],
            'phone'       => $faker->phoneNumber,
            'postal_code' => $faker->postcode,
        ],
        'creator_id'       => 3, // Seeded Sales counter
        'network_id'       => 1, // Kalsel
        'status_code'      => 'de',
        'invoice_id'       => null,
        'rate_id'          => $rate->id,
        'amount'           => $rate->rate_pc,
        'bill_amount'      => $rate->rate_pc,
        'base_rate'        => $rate->rate_pc,
        'reference_no'     => null,
        'payment_type_id'  => 1,
        'customer_id'      => null,
        'pack_type_id'     => 1,
        'costs_detail'     => [
            "base_charge"    => $rate->rate_pc,
            "discount"       => 0,
            "subtotal"       => $rate->rate_pc,
            "insurance_cost" => 0,
            "packing_cost"   => 0,
            "admin_fee"      => 0,
            "add_cost"       => 0,
            "total"          => $rate->rate_pc,
        ],
        'notes'            => null,
        'deleted_at'       => null,
    ];
});

$factory->defineAs(Receipt::class, 'customer', function (Faker\Generator $faker) use ($factory) {
    $receipt = $factory->raw(Receipt::class);

    return array_merge($receipt, [
        'customer_id' => function () {
            return factory(Customer::class)->create()->id;
        },
    ]);
});

$factory->state(Receipt::class, 'invoice_ready', function (Faker\Generator $faker) {
    return ['status_code' => 'ir', 'payment_type_id' => 2];
});

$factory->state(Receipt::class, 'cash', function (Faker\Generator $faker) {
    return ['payment_type_id' => 1];
});

$factory->state(Receipt::class, 'credit', function (Faker\Generator $faker) {
    return ['payment_type_id' => 2];
});

$factory->state(Receipt::class, 'cod', function (Faker\Generator $faker) {
    return ['payment_type_id' => 3];
});

$factory->state(Receipt::class, 'delivered_cod', function (Faker\Generator $faker) {
    return ['payment_type_id' => 3, 'status_code' => array_rand(['dl' => 'dl', 'bd' => 'bd'])];
});
