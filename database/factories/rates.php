<?php

use App\Entities\Customers\Customer;
use App\Entities\Regions\City;
use App\Entities\Regions\District;
use App\Entities\Services\Rate;
use App\Entities\Services\Service;

$factory->define(Rate::class, function (Faker\Generator $faker) {

    $cities = City::all();
    $districts = District::all();
    $origCityId = null;
    $destCityId = null;
    $origDistrictId = 0;
    $destDistrictId = 0;

    $chargeTypeId = rand(1,2); // 1:kg, 2:pc
    $origTypeId = rand(2,3);

    if ($origTypeId == 2) {
        $origCityId = $cities->random()->id;
    } else { // $origTypeId == 3
        $origDistrict = $districts->random();
        $origDistrictId = $origDistrict->id;
        $origCityId = $origDistrict->city_id;
    }

    $destTypeId = rand(2,3);
    if ($destTypeId == 2) {
        $destCityId = $cities->random()->id;
    } else { // $destTypeId == 3
        $destDistrict = $districts->random();
        $destDistrictId = $destDistrict->id;
        $destCityId = $destDistrict->city_id;
    }
    $serviceId = array_rand([11 => 11, 21 => 21]);

    return [
        'customer_id' => 0,
        'service_id' => $serviceId,
        'pack_type_id' => 1,
        'orig_city_id' => $origCityId,
        'orig_district_id' => $origDistrictId,
        'dest_city_id' => $destCityId,
        'dest_district_id' => $destDistrictId,
        'rate_kg' => ($chargeTypeId == 1) ? rand(5, 30) * 1000 : null,
        'rate_pc' => ($chargeTypeId == 2) ? rand(5, 30) * 1000 : null,
        'min_weight' => 1,
        'max_weight' => null,
        'discount' => null,
        'add_cost' => null,
        'etd' => '3-5',
        'notes' => null,
    ];
});

$factory->defineAs(Rate::class, 'city_to_city', function (Faker\Generator $faker) use ($factory) {

    $rate = $factory->raw(Rate::class);

    $cities = City::all();
    $origCityId = $cities->random()->id;
    $destCityId = $cities->random()->id;

    return array_merge($rate, [
        'orig_city_id' => $origCityId,
        'orig_district_id' => 0,
        'dest_city_id' => $destCityId,
        'dest_district_id' => 0,
    ]);
});

$factory->defineAs(Rate::class, 'city_to_district', function (Faker\Generator $faker) use ($factory) {

    $rate = $factory->raw(Rate::class);

    $cities = City::all();
    $districts = District::all();

    $origCityId = $cities->random()->id;
    $destDistrict = $districts->random();
    $destDistrictId = $destDistrict->id;
    $destCityId = $destDistrict->city_id;

    return array_merge($rate, [
        'orig_city_id' => $origCityId,
        'orig_district_id' => 0,
        'dest_city_id' => $destCityId,
        'dest_district_id' => $destDistrictId,
    ]);
});

$factory->defineAs(Rate::class, 'district_to_city', function (Faker\Generator $faker) use ($factory) {

    $rate = $factory->raw(Rate::class);

    $cities = City::all();
    $districts = District::all();

    $origDistrict = $districts->random();
    $origDistrictId = $origDistrict->id;
    $origCityId = $origDistrict->city_id;
    $destCityId = $cities->random()->id;

    return array_merge($rate, [
        'orig_city_id' => $origCityId,
        'orig_district_id' => $origDistrictId,
        'dest_city_id' => $destCityId,
        'dest_district_id' => 0,
    ]);
});

$factory->defineAs(Rate::class, 'district_to_district', function (Faker\Generator $faker) use ($factory) {

    $rate = $factory->raw(Rate::class);

    $cities = City::all();
    $districts = District::all();

    $origDistrict = $districts->random();
    $origDistrictId = $origDistrict->id;
    $origCityId = $origDistrict->city_id;

    $destDistrict = $districts->random();
    $destDistrictId = $destDistrict->id;
    $destCityId = $destDistrict->city_id;

    return array_merge($rate, [
        'orig_city_id' => $origCityId,
        'orig_district_id' => $origDistrictId,
        'dest_city_id' => $destCityId,
        'dest_district_id' => $destDistrictId,
    ]);
});

$factory->defineAs(Rate::class, 'customer', function (Faker\Generator $faker) use ($factory) {
    $rate = $factory->raw(Rate::class);

    return array_merge($rate, [
        'customer_id' => function() {
            return factory(Customer::class)->create()->id;
        },
    ]);
});

$factory->defineAs(Rate::class, 'customer_city_to_city', function (Faker\Generator $faker) use ($factory) {
    $rate = $factory->rawOf(Rate::class, 'city_to_city');

    return array_merge($rate, [
        'customer_id' => function() {
            return factory(Customer::class)->create()->id;
        },
    ]);
});

$factory->defineAs(Rate::class, 'customer_city_to_district', function (Faker\Generator $faker) use ($factory) {
    $rate = $factory->rawOf(Rate::class, 'city_to_district');

    return array_merge($rate, [
        'customer_id' => function() {
            return factory(Customer::class)->create()->id;
        },
    ]);
});

$factory->defineAs(Rate::class, 'customer_district_to_city', function (Faker\Generator $faker) use ($factory) {
    $rate = $factory->rawOf(Rate::class, 'district_to_city');

    return array_merge($rate, [
        'customer_id' => function() {
            return factory(Customer::class)->create()->id;
        },
    ]);
});

$factory->defineAs(Rate::class, 'customer_district_to_district', function (Faker\Generator $faker) use ($factory) {
    $rate = $factory->rawOf(Rate::class, 'district_to_district');

    return array_merge($rate, [
        'customer_id' => function() {
            return factory(Customer::class)->create()->id;
        },
    ]);
});