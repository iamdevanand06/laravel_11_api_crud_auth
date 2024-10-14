<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\CodeCheckController;
use App\Http\Controllers\API\ResetPasswordController;

Route::post('user/register', [RegisterController::class, 'register']);
Route::post('user/login', [RegisterController::class, 'login']);
Route::post('user/forgot-password/get-email', ForgotPasswordController::class);
Route::post('user/forgot-password/verify-code', CodeCheckController::class);
Route::post('user/forgot-password/reset-password', ResetPasswordController::class);

Route::middleware('auth:api')->group( function () {
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::post('logout', [RegisterController::class, 'logout']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
