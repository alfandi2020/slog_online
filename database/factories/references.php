<?php

use App\Entities\References\Reference;

$factory->defineAs(Reference::class, 'comodity', function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'cat' => 'comodity',
    ];
});

$factory->defineAs(Reference::class, 'pack_type', function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'cat' => 'pack_type',
    ];
});
