<?php

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

use function Ramsey\Uuid\v1;

Route::prefix('/cart')->group(function () {
    Route::post('/', [v1\CartController::class, 'store'])->name('cart.store');
    Route::get('/{sessionId}', [V1\CartController::class, 'index'])->name('cart.store');
});
