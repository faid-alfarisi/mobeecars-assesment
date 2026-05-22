<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AppController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('cars', [AppController::class, 'getCars']);
    Route::get('preferences', [AppController::class, 'getPreferences']);
    Route::post('change-password', [AppController::class, 'changePassword']);
    Route::get('global-preferences', [AppController::class, 'globalPreferences']);
    Route::post('save-preferences', [AppController::class, 'savePreferences']);
    Route::post('logout', [AppController::class, 'logout']);
});
