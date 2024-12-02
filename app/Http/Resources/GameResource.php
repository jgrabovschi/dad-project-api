<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BoardResource;
use App\Models\MultiplayerGamesPlayed;

use function Laravel\Prompts\multisearch;
use function PHPSTORM_META\type;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created' => $this?->creator?->nickname ?? 'User Deleted',
            #'create_user_id' => $this->creator->id,
            'winner' => $this->type == 'S' ? null : $this?->winner?->nickname ?? 'User Deleted',
            'type' => $this->type,
            'status' => $this->status,
            'began_at' => $this->began_at,
            'ended_at' => $this->began_at,
            'total_time' => $this->total_time,
            'board_id' => new BoardResource($this->board),
            'total_turns_winner' => $this->total_turns_winner,
            'participants' => $this->type == 'S' ? 
                null : 
                $this->multiplayerGamesPlayed->map(function($multiplayer) {
                    if ($multiplayer->user) {
                        return [
                            'user_nickname' => $multiplayer->user->nickname,
                            'pairs_discovered' => $multiplayer->pairs_discovered,
                        ];
                    }
                    return null;
                })->filter(), // Filter out null values
        ];
    }
}
