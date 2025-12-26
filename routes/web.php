<?php

use App\Http\Controllers\Developer\OAuthClientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('developer')->group(function () {
    Route::get('/', [OAuthClientController::class, 'index'])->name('developer.index');
    // Rate limit client creation to prevent abuse
    Route::post('/clients', [OAuthClientController::class, 'store'])
        ->middleware('throttle:10,60') // 10 requests per hour
        ->name('developer.clients.store');
    Route::delete('/clients/{client}', [OAuthClientController::class, 'destroy'])->name('developer.clients.destroy');
});

require __DIR__.'/settings.php';
