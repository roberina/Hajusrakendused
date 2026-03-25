<?php

use App\Http\Controllers\SharkController;
use Illuminate\Support\Facades\Route;

Route::get('/sharks', [SharkController::class, 'api']);