<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\PeopleController;
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
    ->only(['show'])
    ->name('show', 'people.show');

Route::resource('movies', MovieController::class)->only('show')->name('show', 'movies.show');


require __DIR__ . '/auth.php';
