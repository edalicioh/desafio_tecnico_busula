<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::prefix('/cart')->group(function () {
    Route::post('/', [Api\CartController::class, 'store'])->name('cart.store');
    Route::get('/{sessionId}', [Api\CartController::class, 'index'])->name('cart.store');
});
