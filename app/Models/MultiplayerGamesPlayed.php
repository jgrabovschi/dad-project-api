<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MultiplayerGamesPlayed extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'player_won',
        'pair_discovered'
    ];

    protected $table = "multiplayer_games_played";
    public $timestamps = false;

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
