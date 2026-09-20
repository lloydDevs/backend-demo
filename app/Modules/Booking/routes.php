<?php

use App\Modules\Booking\Http\Controllers\Api\BookingController;
use App\Modules\Booking\Http\Controllers\Api\CustomerController;
use App\Modules\Booking\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware('throttle:30,1')->group(function () {
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('customers', [CustomerController::class, 'index']);
    Route::apiResource('bookings', BookingController::class);
    Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
});
