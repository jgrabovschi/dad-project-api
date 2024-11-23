<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\User;

class GameController extends Controller
{
    public function index()
    {
        return GameResource::collection(Game::paginate(10));
    }

    public function gameByUser(User $user)
    {
        return GameResource::collection(Game::where('created_user_id',$user->id)->paginate(10));
    }

    public function show(Game $game)
    {
        return new GameResource($game);
    }
    
    /*public function destroy(Game $game)
    {
        #$game->multiplayerGamesPlayed()->delete();
        $game->delete();
        return response()->json(null, 204);
    }*/
    public function scoreboardBySingleplayerGames(GetSingleplayerGamesRequest $request)
    {
        $queryParameters = $request->validated();
        $games = Game::query();
        if (array_key_exists('project', $queryParameters)) {
            if ($queryParameters['project'] === null) {
                $tasks->whereNull('project_id');
            } else {
                $tasks->where('project_id', $queryParameters['project']);
            }
        }
        if (array_key_exists('completed', $queryParameters)) {
            $tasks->where('completed', $queryParameters['completed']);
        }
        return GameResource::collection($tasks->get());
    }
}
