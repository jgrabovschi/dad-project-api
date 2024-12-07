<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;
use App\Models\Board;
use App\Models\Transaction;
use App\Models\MultiplayerGamesPlayed;

class StatsController extends Controller
{
    public function getGeneralStats()
    {
        $stats = [
            'games_registered' => Game::count(),
            'games_played' => Game::where('status', 'E')->count(),
            'singleplayer_games_played' => Game::where('type', 'S')->where('status', 'E')->count(),
            'multiplayer_games_played' => Game::where('type', 'M')->where('status', 'E')->count(),
            'users' => User::count(),
            'boards' => Board::count(),
            'transactions' => Transaction::count(),
        ];

        return response()->json($stats);
    }
}
