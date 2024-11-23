<?php

use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\GameController; 
use App\Http\Controllers\api\TransactionController;

//************
// Auth API
//************


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::get('/users/me', [UserController::class , 'showMe']);
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
Route::get('/users/{user}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::post('users/{id}/block', [UserController::class, 'block']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

//************
// Transactions API
//************


Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/users/{user}', [TransactionController::class, 'showUserTransactions']);

//************
// Games API
//************

Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{game}', [GameController::class, 'show']);
Route::get('/games/users/{user}', [GameController::class, 'gameByUser']);
#Route::post('/users', [UserController::class, 'store']);
#Route::put('/users/{id}', [UserController::class, 'update']);
#Route::post('users/{id}/block', [UserController::class, 'block']);
Route::delete('/games/{game}', [GameController::class, 'destroy']);