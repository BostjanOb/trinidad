<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Server;
use Faker\Generator as Faker;

$factory->define(Server::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'ip'   => $faker->ipv4,
    ];
});
