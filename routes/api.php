<?php

use App\Http\Controllers\PeopleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')

    ->group(function () {
        Route::get('/persons', function (Request $request) {
            return response()->json([
                'message' => 'Hello, World!',
            ]);
        });

        Route::resource('people', PeopleController::class)
            ->only(['index', 'show']);
    });
