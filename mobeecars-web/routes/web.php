<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {

    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        return view('dashboard');
    })->name('dashboard');

    // Account Settings
    Route::resource('account', AccountController::class, [
        'names' => [
            'index' => 'account.index',
            'update' => 'account.update',
        ]
    ]);

    // User Management
    Route::post('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::resource('users', UserController::class, [
        'names' => [
            'index' => 'users.index',
            'store' => 'users.store',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]
    ]);

    // Car Inventory
    Route::post('/cars/data', [CarController::class, 'data'])->name('cars.data');
    Route::resource('cars', CarController::class, [
        'names' => [
            'index' => 'cars.index',
            'store' => 'cars.store',
            'update' => 'cars.update',
            'destroy' => 'cars.destroy',
        ]
    ]);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

});
