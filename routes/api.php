<?php

use App\Http\Controllers\SharkController;
use App\Http\Middleware\ValidateApiKey;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateApiKey::class)->group(function () {
    Route::get('/sharks', [SharkController::class, 'api']);
});