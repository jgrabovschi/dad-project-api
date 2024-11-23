<?php

use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

//************
// Auth API
//************



Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    });
Route::post('/auth/login', [AuthController::class, 'login']);




//************
// Boards API
//************




Route::get('/boards', [BoardController::class, 'index']);
Route::post('/boards', [BoardController::class, 'store']);
Route::delete('/boards/{board}', [BoardController::class, 'destroy']);


//************
// Users API
//************

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'showMe']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::post('users/{id}/block', [UserController::class, 'block']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);