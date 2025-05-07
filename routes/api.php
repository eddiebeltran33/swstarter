<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\PeopleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')

    ->group(function () {

        Route::resource('people', PeopleController::class)
            ->only(['index', 'show']);
        Route::resource('movies', MovieController::class)
            ->only(['index', 'show']);
    });
