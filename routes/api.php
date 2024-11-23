<?php

use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\TransactionController;

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
Route::get('/users/{user}', [UserController::class, 'showMe']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::post('users/{user}/block', [UserController::class, 'block']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);


//************
// Transactions API
//************


Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/users/{user}', [TransactionController::class, 'showUserTransactions']);