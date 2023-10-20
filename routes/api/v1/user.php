<?php

use App\Http\Controllers\Api\user\authController;
use Illuminate\Support\Facades\Route;

route::controller(authController::class)->group(function(){
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});

Route::middleware('auth:user-api')->group(function () {
    Route::controller(authController::class)->group(function(){
        Route::post('/logout','logout');
        Route::get('/me','user');
    });
});