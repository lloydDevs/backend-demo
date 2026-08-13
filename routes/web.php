<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
});

// Each module registers its own routes via its Module*ServiceProvider
// (see app/Modules/Product/Providers/ProductModuleServiceProvider.php for an example).
