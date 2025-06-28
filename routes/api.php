<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/token', [TokenController::class, 'getTokenSession']);

Route::post('/payment/store', [TokenController::class, 'storePaymentResponse']);
