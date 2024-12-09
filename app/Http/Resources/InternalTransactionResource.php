<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternalTransactionResource extends JsonResource
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
            'transaction_datetime' => $this->transaction_datetime,
            'user' => $this->user?->nickname,
            'type' => 'Internal',
            'brain_coins' => $this->brain_coins
        ];
    }
}
