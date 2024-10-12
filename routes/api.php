<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
// Route::resource('products', ProductController::class);

Route::middleware('auth:api')->group( function () {
    Route::resource('products', ProductController::class);
    Route::post('logout', [RegisterController::class, 'logout']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
