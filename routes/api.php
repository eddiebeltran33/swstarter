<?php

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\PeopleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')

    ->group(function () {

        Route::resource('people', PeopleController::class)
            ->only(['index', 'show']);
        Route::resource('movies', MovieController::class)
            ->only(['index', 'show']);
    });
