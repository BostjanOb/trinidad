<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain;
use Faker\Generator as Faker;

$factory->define(
    Domain::class,
    function (Faker $faker) {
        return [
            'domain'   => $faker->unique()->domainName,
            'valid_to' => $faker->dateTimeInInterval('now', '+1 year'),
        ];
    }
);
