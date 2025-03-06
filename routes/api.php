<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::get('/user', [UserController::class, 'index'])
    ->middleware('auth:sanctum')
    ->name('index');

Route::get('/user/{id}', [UserController::class, 'show'])
    ->middleware('auth:sanctum')
    ->name('show');

Route::put('/user/{id}', [UserController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('update');

Route::delete('/user/{id}', [UserController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('destroy');

