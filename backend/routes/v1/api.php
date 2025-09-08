<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1;

Route::get('/products', V1\ProductController::class)->name('products.index');
Route::get('/categories', V1\CategoryController::class)->name('categories.index');
Route::get('/payment-methods', V1\PaymentMethodController::class)->name('payment-methods.index');
Route::get('/payment-conditions', V1\PaymentConditionController::class)->name('payment-conditions.index');

require __DIR__ . '/cart.php';
require __DIR__ . '/auth.php';
