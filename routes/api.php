<?php

Route::middleware('auth:api')->group(function () {
    Route::apiResource('users', 'UsersController');
    Route::apiResource('sites', 'SitesController');
    Route::apiResource('servers', 'ServersController');
});



