<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'nickname' => $this->nickname,
            'blocked' => $this->blocked,
            'photo_filename' => $this->photo_filename ? '/storage/public/photos/' . $this->photo_filename : null,
            'brain_coins_balance' => $this->brain_coins_balance,
            'games_won' => $this->gamesWon->count()
        ];
    }
}
