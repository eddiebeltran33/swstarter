<?php

use App\Http\Controllers\Api\MetricController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\PeopleController;
use App\Http\Middleware\InstrumentRequests;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {

        Route::resource('people', PeopleController::class)
            ->only(['index', 'show'])
            ->middleware(InstrumentRequests::class);
        Route::resource('movies', MovieController::class)
            ->only(['index', 'show'])
            ->middleware(InstrumentRequests::class);
        Route::resource('metrics', MetricController::class)
            ->only(['index']);
    });
