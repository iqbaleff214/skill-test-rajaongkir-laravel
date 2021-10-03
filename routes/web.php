<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('home');

Route::get('province', [\App\Http\Controllers\CheckoutController::class, 'get_provinces']);
Route::get('city/{province}', [\App\Http\Controllers\CheckoutController::class, 'get_cities']);
Route::get('cost/{courier}/{weight}/{destination}', [\App\Http\Controllers\CheckoutController::class, 'get_cost']);
