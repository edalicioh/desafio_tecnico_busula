<?php

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

Route::prefix('/cart')->group(function () {
    Route::post('/', [V1\CartController::class, 'store'])->name('cart.store');
    Route::get('/{sessionId}', [V1\CartController::class, 'index'])->name('cart.show');
    Route::post('/{cartId}/recalculate', [V1\CartController::class, 'recalculate'])->name('cart.recalculate');
});
