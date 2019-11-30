<?php

use App\Http\Controllers\DomainsController;
use App\Http\Controllers\ServersController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\UsersController;

Route::middleware('auth:api')->group(
    function () {
        Route::apiResource('users', UsersController::class);
        Route::apiResource('sites', SitesController::class);
        Route::apiResource('servers', ServersController::class);

        Route::apiResource('domains', DomainsController::class)
            ->except(['update']);
    }
);



