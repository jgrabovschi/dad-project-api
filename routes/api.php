<?php

use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
