<?php

use App\Http\Controllers\Api\ConversationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum'], ['prefix' => 'conversations']], function () {
    Route::get('/', [ConversationController::class, 'index']);
    Route::get('/{conversation}', [ConversationController::class, 'show']);
    Route::post('/{conversation}/messages', [MessageController::class, 'store']);
    Route::get('/{conversation}/messages', [MessageController::class, 'index']);
});
