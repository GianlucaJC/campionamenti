<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Accessibile da admin e operatore (sola lettura dashboard)
Route::middleware(['auth', 'role:admin,operatore'])->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('monitoraggi.index');
});

// Accessibile solo da operatore (inserimento campionamento)
Route::middleware(['auth', 'role:operatore'])->group(function () {
    Route::post('/monitoraggi/{section}/checks', [MonitoringController::class, 'store'])
        ->name('monitoraggi.checks.store');
});

// Accessibile solo da admin (gestione struttura)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/monitoraggi/{section}/points', [MonitoringController::class, 'storePoint'])
        ->name('monitoraggi.points.store');
    Route::post('/monitoraggi/{section}/departments', [MonitoringController::class, 'storeDepartment'])
        ->name('monitoraggi.departments.store');
    Route::patch('/monitoraggi/{section}/departments/{department}', [MonitoringController::class, 'updateDepartment'])
        ->name('monitoraggi.departments.update');
    Route::patch('/monitoraggi/{section}/departments/{department}/move', [MonitoringController::class, 'moveDepartment'])
        ->name('monitoraggi.departments.move');
});
