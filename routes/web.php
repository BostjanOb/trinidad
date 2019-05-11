<?php

Auth::routes(['register' => false, 'reset' => false]);

Route::view('/', 'app')
    ->middleware(['auth']);
