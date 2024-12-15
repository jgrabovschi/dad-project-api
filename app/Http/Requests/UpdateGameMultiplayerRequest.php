<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameMultiplayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'winner_user_id' => 'required|integer|exists:users,id',
            'creator_user_id'=> 'required|integer|exists:users,id',
            //o status nao sei se vai ser preciso por causa do I
            'status' => 'required|string|in:E,I',
            'total_time' => 'required|numeric|min:0',
            'player1_pairs_discovered' => 'required|integer|min:0',
            'player2_user_id' => 'required|integer|exists:users,id',
            'player2_pairs_discovered' => 'required|integer|min:0',
            'turns' => 'required|numeric|min:1',
        ];
    }
}
