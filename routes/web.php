<?php

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

Route::get('/', [MonitoringController::class, 'index'])->name('monitoraggi.index');
Route::post('/monitoraggi/{section}/checks', [MonitoringController::class, 'store'])
    ->name('monitoraggi.checks.store');
Route::post('/monitoraggi/{section}/points', [MonitoringController::class, 'storePoint'])
    ->name('monitoraggi.points.store');
