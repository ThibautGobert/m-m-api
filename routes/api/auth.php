<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'auth'], function () {
    Route::get('/user', [AuthController::class, 'user']);
});
