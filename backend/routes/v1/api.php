<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1;

Route::get('/products', V1\ProductController::class)->name('products.index');
Route::get('/categories', V1\CategoryController::class)->name('categories.index');
Route::get('/payment-methods', V1\PaymentMethodController::class)->name('payment-methods.index');
Route::get('/payment-conditions', V1\PaymentConditionController::class)->name('payment-conditions.index');

require __DIR__ . '/cart.php';

// API Authentication routes
Route::post('/auth/login', [V1\Auth\ApiAuthController::class, 'login'])->name('api.auth.login');
Route::post('/auth/logout', [V1\Auth\ApiAuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.auth.logout');
Route::get('/auth/me', [V1\Auth\ApiAuthController::class, 'me'])->middleware('auth:sanctum')->name('api.auth.me');
Route::post('/auth/refresh', [V1\Auth\ApiAuthController::class, 'refresh'])->middleware('auth:sanctum')->name('api.auth.refresh');
