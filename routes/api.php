<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', fn () => auth()->user())->middleware('auth:sanctum');
Route::get('/translations/{locale}/status', [TranslationController::class, 'status']);
Route::get('/translations/{locale}/concept/{concept}', [TranslationController::class, 'get']);
//Route::get('/translations/{locale}/{concept}/version', [TranslationController::class, 'version']);


Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/api/auth.php';
require __DIR__.'/api/conversation.php';
require __DIR__.'/api/user.php';
