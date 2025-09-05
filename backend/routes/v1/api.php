<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

Route::get('/products', Api\ProductController::class)->name('products.index');
Route::get('/categories', Api\CategoryController::class)->name('categories.index');

require __DIR__ . '/cart.php';
require __DIR__ . '/auth.php';
