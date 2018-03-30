<?php

use Faker\Generator as Faker;

$factory->define(App\Model\Material::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\Model\Users::class)->create()->id;
        },
        'title' => $faker->realText($faker->numberBetween(10,20)),
        'body' => $faker->paragraph(),
        'public' => true,
        'status' => true,
    ];
});

$factory->state(App\Model\Material::class, 'public', function (Faker $faker) {
    return [
        'public' => true,
    ];
});

$factory->state(App\Model\Material::class, 'private', function (Faker $faker) {
    return [
        'public' => false,
    ];
});
