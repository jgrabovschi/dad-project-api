<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    /**
     * Create a new policy instance.
     */
    public function before(User $user, string $ability): bool|null 
    { 
        if ($user->type == 'A') { 
            return true; 
        } 
        return null; 
    } 
 
    public function viewAny(?User $user): bool 
    { 
        
        return ($user->type == 'P') || ($user->type == 'A') ; 
    } 
    public function view(User $user, Game $game): bool 
    { 
        return true; 
    } 
 
    public function create(User $user): bool 
    { 
        return $user->type == 'P'; 
    } 
 
    public function update(User $user, Game $game): bool 
    { 
        if($game->created_user_id == $user->id){
            return true;
        }
        
        return false; 
    } 

    public function updateMulti(User $user, Game $game): bool 
    { 

        foreach ($game->multiplayerGamesPlayed as $multiGame) {
            if($multiGame->user_id == $user->id){
                return true;
            }
        }
        return false; 
    } 

    
 
    
}
