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
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\JoinGameRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class GameController extends Controller
{
    public function index(Request $request)
    {
        if($request->user()->type == 'A')
        {
            return GameResource::collection(Game::paginate(10));
        }
        else
        {
            $ids = MultiplayerGamesPlayed::where('user_id', $request->user()->id)->pluck('game_id')->toArray();
            return GameResource::collection(Game::whereIntegerInRaw('id', $ids)->paginate(10));
        }
    }

    public function gameByUser(User $user)
    {
        return GameResource::collection(Game::where('created_user_id',$user->id)->paginate(10));
    }

    public function show(Game $game)
    {
        return new GameResource($game);
    }
    
    public function store(StoreGameRequest $request)
    {
        
        $validated = $request->validated();
        $game = null;

        if($validated['type'] == 'S'){
            if($validated['board_id'] == 1){
                $game = new Game();
                $game->fill($validated);
                $game->status = 'PL';
                $game->began_at = now();
                $game->ended_at = null;
            }else{
                $user = User::findOrFail($validated['created_user_id']);
                if($user->brain_coins_balance < 1){
                    return response()->json([
                        'message' => 'User needs to have 1 brain coins to play a single player game in this board'
                    ], 400);
                }

                $game = new Game();
                $game->fill($validated);
                $game->status = 'PL';
                $game->began_at = now();
                $game->ended_at = null;
                $user->brain_coins_balance--;

                if($game->save() && $user->save()){
                    
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->game_id = $game->id;
                    $transaction->brain_coins = -1;
                    $transaction->type = 'I';
                    $transaction->transaction_datetime = now();
                    $transaction->save();               
                }
            }
        }else{
            $user = User::findOrFail($validated['created_user_id']);
                if($user->brain_coins_balance < 5){
                    return response()->json([
                        'message' => 'User needs to have 5 brain coins to play a Multi player game'
                    ], 400);
                }

                $game = new Game();
                $game->fill($validated);
                $game->status = 'PE';
                $game->began_at = now();
                $game->ended_at = null;
                $user->brain_coins_balance -= 5;

                if($game->save() && $user->save()){
                    
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->game_id = $game->id;
                    $transaction->brain_coins = -5;
                    $transaction->type = 'I';
                    $transaction->transaction_datetime = now();
                    $transaction->save();            
                }

                $multiplayerGame = new MultiplayerGamesPlayed();

                $multiplayerGame->user_id = $user->id;
                $multiplayerGame->game_id = $game->id;
                $multiplayerGame->save();
        }
        #$task = Task::create($request->validated());
        return new GameResource($game);
    }

    public function update(UpdateGameRequest $request, Game $game)
    {
        //pode ser preciso melhor proteções
        if($game->status == 'I'){
            return response()->json([
                'message' => 'Can´t update game has already ended'
            ], 400);
        }
        $validated = $request->validated();

        /*if($game->created_user_id != $validated['winner_user_id']){
            if($game->status == 'E' || $game->status == 'I'){
                return response()->json([
                    'message' => 'Can´t update game has already ended'
                ], 400);
            }
        }*/
        
        
        
        if($game->type == 'M'){
            //tou a pensar dividir isto este dois endpoints um para quem ganhou e um para quem perdeu
            if($game->winner_user_id != null){
                return response()->json([
                    'message' => 'Can´t update game has already ended'
                ], 400);
            }
            $multiplayerGame = MultiplayerGamesPlayed::where('game_id', $game->id)
                ->where('user_id', $validated['user_id'])->get()[0];
            if($multiplayerGame->user_id == $validated['winner_user_id']){

                $game->status = 'E';
                $game->ended_at = now();
                
                $start = Carbon::parse($game->created_at);
                $end = Carbon::parse($game->ended_at);
                
                $secondsPassed = $start->floatDiffInSeconds($end);
                $game->total_time = number_format($secondsPassed, 2);

                $game->winner_user_id = $validated['winner_user_id'];
                $game->save();

                //dar as coins ao user que ganhou
                $user = User::findOrFail($validated['winner_user_id']);
                $user->brain_coins_balance += 8;
                $user->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->game_id = $game->id;
                $transaction->brain_coins = 8;
                $transaction->type = 'I';
                $transaction->transaction_datetime = now();
                $transaction->save(); 
                $multiplayerGame->player_won = 1;
            }else{
                if($multiplayerGame->player_won != null){
                    return response()->json([
                        'message' => 'Can´t update game has already ended'
                    ], 400);
                }
                $multiplayerGame->player_won = 0;
            }

            $multiplayerGame->pairs_discovered = $validated['pairs_discovered'];
            $multiplayerGame->save();
        }else{
            //fazer singleplayer coisa de update
            $game->status = 'E';
            $game->ended_at = now();
            
            $start = Carbon::parse($game->created_at);
            $end = Carbon::parse($game->ended_at);
            
            $secondsPassed = $start->floatDiffInSeconds($end);
            $game->total_time = number_format($secondsPassed, 2);

            $game->save();
        }

        return new GameResource($game);
    }

    public function join(JoinGameRequest $request, Game $game)
    {
        if($game->status != 'PE'){
            return response()->json([
                'message' => 'Can only join a peding game'
            ], 400);
        }

        $validated = $request->validated();

        $userExists = $game->multiplayerGamesPlayed()->where('user_id', $validated['user_id'])->exists();

        if($userExists){
            return response()->json([
                'message' => "Can´t join the same game twice"
            ], 400); 
        }
        
        $multiplayerGame = new MultiplayerGamesPlayed();

        $multiplayerGame->user_id = $validated['user_id'];
        $multiplayerGame->game_id = $game->id;
        $multiplayerGame->save();

        $game->status = 'PL';
        $game->save();

        return new GameResource($game);
    }
    /*public function destroy(Game $game)
    {
        #$game->multiplayerGamesPlayed()->delete();
        $game->delete();
        return response()->json(null, 204);
    }*/
    
}
