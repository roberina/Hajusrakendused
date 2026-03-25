<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SharkController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
    Route::post('/weather/search', [WeatherController::class, 'search'])->name('weather.search');

    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::post('/map', [MapController::class, 'store'])->name('map.store');
    Route::put('/map/{marker}', [MapController::class, 'update'])->name('map.update');
    Route::delete('/map/{marker}', [MapController::class, 'destroy'])->name('map.destroy');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/blog/{post}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [BlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('/blog/{post}/comments', [BlogController::class, 'storeComment'])->name('blog.comment.store');
    Route::delete('/blog/{post}/comments/{comment}', [BlogController::class, 'destroyComment'])->name('blog.comment.destroy');

    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
    Route::post('/shop/confirm', [ShopController::class, 'confirm'])->name('shop.confirm');

    Route::get('/sharks', [SharkController::class, 'index'])->name('sharks.index');
    Route::get('/sharks/create', [SharkController::class, 'create'])->name('sharks.create');
    Route::post('/sharks', [SharkController::class, 'store'])->name('sharks.store');
    Route::get('/sharks/{shark}/edit', [SharkController::class, 'edit'])->name('sharks.edit');
    Route::put('/sharks/{shark}', [SharkController::class, 'update'])->name('sharks.update');
    Route::delete('/sharks/{shark}', [SharkController::class, 'destroy'])->name('sharks.destroy');
});

require __DIR__.'/auth.php';