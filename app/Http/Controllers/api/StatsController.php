<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;
use App\Models\Board;
use App\Models\Transaction;
use App\Models\MultiplayerGamesPlayed;
use Illuminate\Support\Facades\DB;

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
            'games_per_month' => DB::table('games')
                                ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total_games")
                                ->where('status', 'E') // Ended Games
                                ->groupByRaw("YEAR(created_at), MONTH(created_at)") // Group by year and month
                                ->orderByRaw("YEAR(created_at) ASC, MONTH(created_at) ASC")
                                ->get()
            ,
        ];

        return response()->json($stats);
    }

    public function myStats(Request $request)
    {
        $ids = MultiplayerGamesPlayed::where('user_id', $request->user()->id)->pluck('game_id')->toArray();

        $stats = [
            'singleplayer_games_played' => Game::where('type', 'S')->where('status', 'E')->where('created_user_id', $request->user()->id)->count(),
            'multiplayer_games_played' => count($ids),
            'transactions' => Transaction::where('user_id', $request->user()->id)->count(),
            'brain_coins_balance' => $request->user()->brain_coins_balance,
            'total_euro_spent' => Transaction::where('user_id', $request->user()->id)->where('type', 'P')->sum('euros'),
            'wins_per_month' => DB::table('games')
                                ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total_wins")
                                ->where('status', 'E') // Ended Games
                                ->where('winner_user_id', $request->user()->id) // User is the winner
                                ->groupByRaw("YEAR(created_at), MONTH(created_at)") // Group by year and month
                                ->orderByRaw("YEAR(created_at) ASC, MONTH(created_at) ASC")
                                ->get(),
            'losses_per_month' => DB::table('games')
                                ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total_losses")
                                ->where('status', 'E') // Ended Games
                                ->whereIntegerInRaw('id', $ids) // User played the game
                                ->where('winner_user_id', '!=', $request->user()->id) // User is not the winner
                                ->where('type', 'M') // Multiplayer games
                                ->groupByRaw("YEAR(created_at), MONTH(created_at)") // Group by year and month
                                ->orderByRaw("YEAR(created_at) ASC, MONTH(created_at) ASC")
                                ->get(),
        ];

        return response()->json($stats);
    }

    public function adminStats(Request $request)
    {
        // query the user that spent the highest sum money on the platform on all the purchases
        $userSpentMost = DB::table('transactions')
                        ->selectRaw('user_id, SUM(euros) as total_euros_spent')
                        ->where('type', 'P') // Purchases
                        ->groupBy('user_id')
                        ->orderBy('total_euros_spent', 'DESC')
                        ->first();
        
        $stats = [
            'total_revenue' => Transaction::where('type', 'P')->sum('euros'),
            'user_most_money_spent' => [
                'user' => User::where('id', $userSpentMost->user_id)->first(),
                'total_euros_spent' => $userSpentMost->total_euros_spent
            ],
            'revenue_per_month' => DB::table('transactions')
                                ->selectRaw("YEAR(transaction_datetime) as year, MONTH(transaction_datetime) as month, SUM(euros) as total_revenue")
                                ->where('type', 'P') // Purchases
                                ->groupByRaw("YEAR(transaction_datetime), MONTH(transaction_datetime)") // Group by year and month
                                ->orderByRaw("YEAR(transaction_datetime) ASC, MONTH(transaction_datetime) ASC")
                                ->get(),
            'total_blocks' => User::where('blocked', 1)->count(), 
            'transactions_per_month' => DB::table('transactions')
                                ->selectRaw("YEAR(transaction_datetime) as year, MONTH(transaction_datetime) as month, COUNT(*) as total_transactions")
                                ->groupByRaw("YEAR(transaction_datetime), MONTH(transaction_datetime)") // Group by year and month
                                ->orderByRaw("YEAR(transaction_datetime) ASC, MONTH(transaction_datetime) ASC")
                                ->get(),
            'average_euros_per_transaction' => Transaction::where('type', 'P')->avg('euros'),
        ];
        
        return response()->json($stats);
    }
}
