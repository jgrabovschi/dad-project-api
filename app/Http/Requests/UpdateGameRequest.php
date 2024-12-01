<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
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
            'winner_user_id' => 'sometimes|integer|exists:users,id',
            'user_id'=> 'sometimes|integer|exists:users,id',
            'pairs_discovered' => 'sometimes|integer'
            //o status nao sei se vai ser preciso por causa do I
            'status' => 'required|string|in:E,I',
        ];
    }
}
