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

use function PHPSTORM_META\map;

class ScoreboardController extends Controller
{
    public function scoreboardBySingleplayerGames(string $filter)
    {
        if ($filter != 'turns' && $filter != 'time') 
        {
            return response()->json(['error' => 'Invalid filter'], 400);
        }

        $games = Game::query();

        
        if($filter == 'turns')
        {
            $games->selectRaw('board_id, ANY_VALUE(users.nickname) as nickname, MIN(total_turns_winner) as total_turns_winner')
              ->join('users', 'games.created_user_id', '=', 'users.id')
              ->whereNull('users.deleted_at')
              ->where('games.type', 'S')
              ->where('games.status', 'E')
              ->groupBy('board_id')
              ->orderBy('total_turns_winner', 'asc');
        }
        else
        {
            $games->selectRaw('board_id, ANY_VALUE(users.nickname) as nickname, MIN(total_time) as total_time')
              ->join('users', 'games.created_user_id', '=', 'users.id')
              ->whereNull('users.deleted_at')
              ->where('games.type', 'S')
              ->where('games.status', 'E')
              ->groupBy('board_id')
              ->orderBy('total_time', 'asc');
        }

        $bestScores = $games->with(['board', 'creator'])->get();

        $result = $bestScores->map(function($game, $filter) {
            return [
                'board' => $game->board->board_rows . 'x' . $game->board->board_cols,
                'performance' => $filter == 'turns' ? $game->total_turns_winner : $game->total_time,
                'user' => $game->nickname,
            ];
        });

        return $result;
       

    }

    public function scoreboardBySingleplayerGamesByUsers(Request $request, string $filter)
    {
        if ($filter != 'turns' && $filter != 'time') 
        {
            return response()->json(['error' => 'Invalid filter'], 400);
        }

        $games = Game::query();
        $games->selectRaw('board_id, MIN(total_turns_winner) as total_turns_winner, MIN(total_time) as total_time')
              ->where('games.type', 'S')
              ->where('games.status', 'E')
              ->where('games.created_user_id', $request->user()->id)
              ->groupBy('board_id');
        
        if($filter == 'turns')
        {
            $games->orderBy('total_turns_winner', 'asc');
        }
        else
        {
            $games->orderBy('total_time', 'asc');
        }

        $bestScores = $games->with('board')->get();

        //same as the global, but without the user because we are 
        //returning info about the user that requested the scoreboard
        $result = $bestScores->map(function($game, $filter) {
            return [
                'board' => $game->board->board_rows . 'x' . $game->board->board_cols,
                'performance' => $filter == 'turns' ? $game->total_turns_winner : $game->total_time,
            ];
        });

        return $result;
       
    }

    public function scoreboardByMultiplayerGames(string $filter)
    {
        if ($filter != 'wins' && $filter != 'losses') 
        {
            return response()->json(['error' => 'Invalid filter'], 400);
        }

        $games = Game::query();
        $bestScores = $games->selectRaw('board_id, ANY_VALUE(users.nickname) as winner, COUNT(*) as wins, MAX(games.ended_at) as last_game')
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->join('users', 'multiplayer_games_played.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at') // do not consider deleted users
            ->where('games.type', 'M')
            ->where('games.status', 'E')
            ->where('player_won', $filter == 'wins' ? 1 : 0) //if it's wins, player_won = 1, if it's losses, player_won = 0
            ->groupBy('board_id', 'multiplayer_games_played.user_id')
            ->with('board')
            ->orderBy('wins', 'desc')
            ->orderBy('last_game', 'asc') //if two players have the same number of wins, the one that won/lost first will be first
            ->get();


        $result = $bestScores->groupBy('board_id')->map(function($games) {
            $topPlayers = $games->take(5);

            return [
                'board' => $topPlayers[0]->board->board_rows . 'x' . $topPlayers[0]->board->board_cols,
                'players' => $topPlayers->map(function($game) {
                    return [
                        'user' => $game->winner,
                        'games' => $game->wins, //it can be losses too
                    ];
                }),
            ];
        })->values();

        
        return $result;
    }

    public function scoreboardByMutliplayerGamesByUsers(Request $request, string $filter)
    {
        if ($filter != 'wins' && $filter != 'losses') 
        {
            return response()->json(['error' => 'Invalid filter'], 400);
        }

        $games = Game::query();
        $bestScores = $games->selectRaw('board_id, COUNT(*) as wins')
            ->join('multiplayer_games_played', 'games.id', '=', 'multiplayer_games_played.game_id')
            ->where('multiplayer_games_played.user_id', $request->user()->id)
            ->where('games.type', 'M')
            ->where('games.status', 'E')
            ->where('player_won', $filter == 'wins' ? 1 : 0) //if it's wins, player_won = 1, if it's losses, player_won = 0
            ->groupBy('board_id')
            ->with('board')
            ->orderBy('wins', 'desc')
            ->get();


        $result = $bestScores->map(function($game) {
            return [
                'board' => $game->board->board_rows . 'x' . $game->board->board_cols,
                'games' => $game->wins, //it can mean losses too
            ];
        })->values();

        
        return $result;
    }
}
