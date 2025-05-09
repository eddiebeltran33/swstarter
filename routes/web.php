<?php

use App\Http\Controllers\MetricController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PeopleController;
use App\Http\Middleware\InstrumentRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // redirect to the dashboard
    return redirect()->route('dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');


Route::resource('people', PeopleController::class)
    ->middleware(InstrumentRequests::class)
    ->only(['show'])
    ->name('show', 'people.show');

Route::resource('movies', MovieController::class)
    ->middleware(InstrumentRequests::class)
    ->only('show')
    ->name('show', 'movies.show');

Route::resource('metrics', MetricController::class)
    ->only(['index'])
    ->name('index', 'metrics.index');


require __DIR__ . '/auth.php';
