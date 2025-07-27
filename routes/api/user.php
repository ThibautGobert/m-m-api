<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users'], function () {
    Route::get('/cards', [UserController::class, 'cards']);
});

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'users'], function () {
    route::get('/{uuid}', [UserController::class, 'get']);
});
