<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BoardResource;

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
            'create_user_id' => new UserResource($this->creator),
            #'create_user_id' => $this->creator->id,
            'winner_user_id' => new UserResource($this->winner),
            'type' => $this->type,
            'status' => $this->status,
            'began_at' => $this->began_at,
            'began_at' => $this->began_at,
            'total_time' => $this->total_time,
            'board_id' => new BoardResource($this->board),

        ];
    }
}
