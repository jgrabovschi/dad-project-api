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
use App\Models\Transaction;
use App\Models\User;
use App\Models\Game;


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
    Route::get('/stats/admin', [StatsController::class, 'adminStats'])->can('admin') ; //only for admin stats
    
    //Admin
    Route::get('/users', [UserController::class, 'index'])->can('viewAny', User::class); //show all users


    //game
    Route::post('/games', [GameController::class, 'store'])->can('create', Game::class);
    Route::put('/games/{game}', [GameController::class, 'update'])->can('update', 'game');
    Route::put('/games/multiplayer/{game}', [GameController::class, 'updateMulti'])->can('updateMulti', 'game');
    Route::get('/games/{game}', [GameController::class, 'show'])->can('view', 'game');
    // Route::post('/games', [GameController::class, 'store']);
    // Route::put('/games/{game}', [GameController::class, 'update']);
    // Route::put('/games/multiplayer/{game}', [GameController::class, 'updateMulti']);


    //************
    // Transactions API
    //************

    Route::get('/transactions', [TransactionController::class, 'index'])->can('admin');
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/users/{nickname}', [TransactionController::class, 'showUserTransactions']);
    Route::get('/transactions/users/{nickname}/type/{type}', [TransactionController::class, 'showTransactionsByTypeAndUser']);
    Route::get('/transactions/type/{type}', [TransactionController::class, 'showTransactionsByType']);
    
    //users
    Route::post('users/{user}/block', [UserController::class, 'block'])->can('admin');
    Route::post('/users/admin', [UserController::class, 'store'])->can('admin');
    Route::put('/users/{user}', [UserController::class, 'update'])->can('update','user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->can('delete', 'user');
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







//************
// Games API
//************

//Route::get('/games/{game}', [GameController::class, 'show']);
//Route::post('/games', [GameController::class, 'store']);
//Route::put('/games/{game}', [GameController::class, 'update']);
//Route::put('/games/multiplayer/{game}', [GameController::class, 'updateMulti']);
//Route::put('/games/{game}/join', [GameController::class, 'join']);
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