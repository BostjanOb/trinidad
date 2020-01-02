<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Checker;
use Faker\Generator as Faker;

$factory->define(Checker::class, function (Faker $faker) {
    $site = factory(\App\Site::class)->create();

    return [
        'checkable_type' => get_class($site),
        'checkable_id'   => $site->id,
        'arguments'      => [],
        'checker'        => 'TestChecker',
    ];
});
