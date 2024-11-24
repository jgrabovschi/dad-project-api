<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use App\Http\Resources\UserResource;
use App\Models\Game;
use App\Models\User;
use App\Models\Transaction;
use App\Models\MultiplayerGamesPlayed;
use App\Http\Requests\GetSingleplayerGamesRequest;
use App\Http\Requests\GetMultiplayerGamesRequest;
use Illuminate\Support\Facades\DB;

class ScoreboardController extends Controller
{
    public function scoreboardBySingleplayerGames(GetSingleplayerGamesRequest $request)
    {
        $queryParameters = $request->validated();
        $games = Game::query();
        $games->where('board_id', $queryParameters['board_id'])->where('status', 'E');
        if($queryParameters['performance'] == 'turns'){
            $games->orderBy('total_time', 'asc');
        }else
        {
            $games->orderBy($queryParameters['performance'], 'asc');
        }
        
        return GameResource::collection($games->limit(10)->get());
    }

    public function scoreboardBySingleplayerGamesByUsers(GetSingleplayerGamesRequest $request, User $user)
    {
        $queryParameters = $request->validated();
        $games = Game::query();
        $games->where('board_id', $queryParameters['board_id'])->where('created_user_id', $user->id)->where('status', 'E');
        if($queryParameters['performance'] == 'turns'){
            $games->orderBy('total_time', 'asc');
        }else
        {
            $games->orderBy($queryParameters['performance'], 'asc');
        }
        
        return GameResource::collection($games->limit(10)->get());
    }

    public function scoreboardByMultiplayerGames(GetMultiplayerGamesRequest $request)
    {

        $queryParameters = $request->validated();
        $topPlayers = DB::table('games')
            ->select('winner_user_id', DB::raw('COUNT(*) as wins'))
            ->where('board_id',$queryParameters['board_id'])
            ->whereNotNull('winner_user_id') // Exclude games without a winner
            ->groupBy('winner_user_id') // Group by the winner ID
            ->orderBy('wins', 'desc') // Order by the number of wins, descending
            ->take(5) // Limit to the top 5 players
            ->get();
        

        // Extract winner_user_ids
        $winnerUserIds = $topPlayers->pluck('winner_user_id')->toArray();

        // Query Users (fetching them in one query to avoid multiple calls)
        $users = User::whereIn('id', $winnerUserIds)->get()->keyBy('id');

        // Map the users onto the top players
        $topPlayers = $topPlayers->map(function ($player) use ($users) {
            // Check if the user exists in the collection and add the nickname field
            if (isset($users[$player->winner_user_id])) {
                $player->nickname = $users[$player->winner_user_id]->nickname;
            } else {
                $player->nickname = null; // Default value if user not found
            }
            
            return $player;
        });
        
        return $topPlayers;
        
    }

    public function scoreboardByMutliplayerGamesByUsers(GetMultiplayerGamesRequest $request, User $user)
    {
        $queryParameters = $request->validated();
        $topPlayers = DB::table('games')
            ->select('winner_user_id', DB::raw('COUNT(*) as wins'))
            ->where('winner_user_id', $user->id)
            ->where('board_id',$queryParameters['board_id'])
            ->whereNotNull('winner_user_id') // Exclude games without a winner
            ->groupBy('winner_user_id') // Group by the winner ID
            ->orderBy('wins', 'desc') // Order by the number of wins, descending
            ->take(5) // Limit to the top 5 players
            ->get();
            
        $losses = DB::table('games')
            ->select(DB::raw('COUNT(*) as losses'))
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->where('multiplayer_games_played.user_id', $user->id)
            ->where('winner_user_id', '!=', $user->id) // Fix here
            ->where('board_id', $queryParameters['board_id'])
            ->whereNotNull('winner_user_id') // Exclude games without a winner
            ->get();

        // Map the users onto the top players
        $topPlayers = $topPlayers->map(function ($player) use ($losses) {
            // Check if the user exists in the collection and add the nickname field
            
            $player->losses = $losses[0]->losses; // Default value if user not found
            
            
            return $player;
        });
        
        return $topPlayers;
    }
}
