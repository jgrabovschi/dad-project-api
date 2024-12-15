<?php

use App\Http\Controllers\api\BoardController;
use App\Http\Controllers\api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\GameController; 
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\ScoreboardController;
use App\Http\Controllers\api\StatsController;
use App\Models\User;

//************
// Auth API
//************


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::get('/users/me', [UserController::class , 'showMe']);
    Route::post('auth/validatepassword', [AuthController::class, 'validatePassword']);
    
    //games
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/multiplayer', [GameController::class, 'multiplayerGames']);
    
    //scoreboards
    Route::get('/scoreboards/singleplayer/personal/{filter}', [ScoreboardController::class, 'scoreboardBySingleplayerGamesByUsers']);
    Route::get('/scoreboards/multiplayer/personal/{filter}', [ScoreboardController::class, 'scoreboardByMutliplayerGamesByUsers']);

    //stats
    Route::get('/stats/my', [StatsController::class, 'myStats']); //my stats
    Route::get('/stats/admin', [StatsController::class, 'adminStats']); //only for admin stats
    
    //Admin
    Route::get('/users', [UserController::class, 'index'])->can('viewAny', User::class); //show all users


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


Route::get('/users/{user}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::post('users/{user}/block', [UserController::class, 'block']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);


//************
// Transactions API
//************


Route::get('/transactions', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/users/{nickname}', [TransactionController::class, 'showUserTransactions']);
Route::get('/transactions/users/{nickname}/type/{type}', [TransactionController::class, 'showTransactionsByTypeAndUser']);
Route::get('/transactions/type/{type}', [TransactionController::class, 'showTransactionsByType']);


//************
// Games API
//************

Route::get('/games/{game}', [GameController::class, 'show']);
Route::get('/games/users/{user}', [GameController::class, 'gameByUser']);
Route::post('/games', [GameController::class, 'store']);
Route::put('/games/{game}', [GameController::class, 'update']);
Route::put('/games/multiplayer/{game}', [GameController::class, 'updateMulti']);
Route::put('/games/{game}/join', [GameController::class, 'join']);
#Route::post('/users', [UserController::class, 'store']);
#Route::put('/users/{id}', [UserController::class, 'update']);
#Route::post('users/{id}/block', [UserController::class, 'block']);
#Route::delete('/games/{game}', [GameController::class, 'destroy']);


//************
// Scoreboard API
//************
Route::get('/scoreboards/singleplayer/global/{filter}', [ScoreboardController::class, 'scoreboardBySingleplayerGames']);
Route::get('/scoreboards/multiplayer/global/{filter}', [ScoreboardController::class, 'scoreboardByMultiplayerGames']);

//************
// Stats API
//************

Route::get('/stats', [StatsController::class, 'getGeneralStats']);