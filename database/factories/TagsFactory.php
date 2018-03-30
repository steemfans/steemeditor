<?php

use Faker\Generator as Faker;

$factory->define(App\Model\Tags::class, function (Faker $faker) {
    return [
        'tag_content' => $faker->word,
        'public' => true,
    ];
});
