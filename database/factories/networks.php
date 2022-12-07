<?php

use App\Entities\Networks\DeliveryUnit;
use App\Entities\Networks\Network;
use App\Entities\Regions\City;
use App\Entities\Regions\District;

$factory->define(Network::class, function (Faker\Generator $faker) {

    $typeId = rand(1, 4);
    $origDistrictId = null;

    if (in_array($typeId, [1, 2])) {
        if ($typeId == 1) {
            // dump(DB::table('networks')->select('code')->get());
            $origCityId = City::where('id', 'not like', '63%')->get()->random()->id;
            $code = substr($origCityId, 0, 2).'000000';
        } else {
            $origCityId = City::all()->random()->id;
            $code = $origCityId.'0000';
        }
    } else {
        $district = District::all()->random();
        $origDistrictId = $district->id;
        $origCityId = $district->city_id;

        if ($typeId == 3) {
            $code = $origDistrictId.'0';
        } else {
            $code = $origDistrictId.rand(1, 9);
        }

    }

    return [
        'type_id'            => $typeId,
        'code'               => $code,
        'name'               => $faker->name,
        'address'            => $faker->address,
        'coordinate'         => '0.0000,0.0000',
        'postal_code'        => '70000',
        'phone'              => '081234567890',
        'email'              => 'network@mail.com',
        'origin_city_id'     => $origCityId,
        'origin_district_id' => $origDistrictId,
    ];
});

$factory->state(Network::class, 'province', function (Faker\Generator $faker) {

    $origCityId = City::where('id', 'not like', '63%')->get()->random()->id;

    return [
        'type_id'            => 1,
        'code'               => substr($origCityId, 0, 2).'000000',
        'name'               => $faker->name,
        'address'            => $faker->address,
        'coordinate'         => '0.0000,0.0000',
        'postal_code'        => '70000',
        'phone'              => '081234567890',
        'email'              => 'network@mail.com',
        'origin_city_id'     => $origCityId,
        'origin_district_id' => null,
    ];
});

$factory->state(Network::class, 'city', function (Faker\Generator $faker) {

    $origCityId = City::all()->random()->id;

    return [
        'type_id'            => 2,
        'code'               => $origCityId.'0000',
        'name'               => $faker->name,
        'address'            => $faker->address,
        'coordinate'         => '0.0000,0.0000',
        'postal_code'        => '70000',
        'phone'              => '081234567890',
        'email'              => 'network@mail.com',
        'origin_city_id'     => $origCityId,
        'origin_district_id' => null,
    ];
});

$factory->state(Network::class, 'district', function (Faker\Generator $faker) {
    $district = District::all()->random();
    $origDistrictId = $district->id;
    $origCityId = $district->city_id;

    return [
        'type_id'            => 3,
        'code'               => $origDistrictId.'0',
        'name'               => $faker->name,
        'address'            => $faker->address,
        'coordinate'         => '0.0000,0.0000',
        'postal_code'        => '70000',
        'phone'              => '081234567890',
        'email'              => 'network@mail.com',
        'origin_city_id'     => $origCityId,
        'origin_district_id' => $origDistrictId,
    ];
});

$factory->state(Network::class, 'outlet', function (Faker\Generator $faker) {
    $district = District::all()->random();
    $origDistrictId = $district->id;
    $origCityId = $district->city_id;
    $outletCodeRange = range(1, 9);
    $code = $origDistrictId.$outletCodeRange[array_rand($outletCodeRange)];

    return [
        'type_id'            => 4,
        'code'               => $code,
        'name'               => $faker->name,
        'address'            => $faker->address,
        'coordinate'         => '0.0000,0.0000',
        'postal_code'        => '70000',
        'phone'              => '081234567890',
        'email'              => 'network@mail.com',
        'origin_city_id'     => $origCityId,
        'origin_district_id' => $origDistrictId,
    ];
});

$factory->define(DeliveryUnit::class, function (Faker\Generator $faker) {

    return [
        'name'        => 'Delivery Unit '.rand(1, 50),
        'plat_no'     => str_random(10),
        'type_id'     => rand(1, 5),
        'network_id'  => 1, // Seeded BAM Kalsel
        'description' => $faker->sentence,
    ];
});
