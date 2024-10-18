<?php

use App\Http\Controllers\API\Auth\CodeCheckController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ExcelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [RegisterController::class, 'register']);
Route::post('user/login', [RegisterController::class, 'login']);
Route::post('user/forgot-password/get-email', ForgotPasswordController::class);
Route::post('user/forgot-password/verify-code', CodeCheckController::class);
Route::post('user/forgot-password/reset-password', ResetPasswordController::class);

Route::middleware('auth:api')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::get('products-export', [ExcelController::class, 'downloadProduct']);
    // Route::post('products-import', [ExcelController::class, 'uploadProduct']);
    Route::post('users-import', [ExcelController::class, 'uploadUser']);
    Route::get('users-export', [ExcelController::class, 'downloadUser']);
    Route::post('logout', [RegisterController::class, 'logout']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
