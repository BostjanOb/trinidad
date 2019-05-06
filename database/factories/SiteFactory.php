<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Site;
use Faker\Generator as Faker;

$factory->define(Site::class, function (Faker $faker) {
    return [
        'server_id' => function () {
            return factory(App\Server::class)->create()->id;
        },
        'name'      => $faker->domainWord,
        'domain'    => $faker->unique()->domainName,
    ];
});
