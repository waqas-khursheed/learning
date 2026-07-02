<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Presentation layer ka entry point
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
