<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->type == 'B'){
            $type = 'Bonus';
        }elseif($this->type == 'P'){
            $type = 'Purchase';
        }else{
            $type = 'Internal';
        }

        return [
            'id' => $this->id,
            'transaction_datetime' => $this->transaction_datetime,
            'user' => $this->user?->nickname,
            'type' => $type,
            'euros' => $this->euros,
            'brain_coins' => $this->brain_coins,
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference,
        ];
    }
}
