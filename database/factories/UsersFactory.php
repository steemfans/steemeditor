<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Model\Users::class, function (Faker $faker) {
    $time = time();
    return [
        'username' => $faker->name,
        'origin_data' => '',
        'token' => 'test_token_'.$faker->name,
        'expire' => $time,
        'last_login' => $time,
        'created_at' => $time,
    ];
});
