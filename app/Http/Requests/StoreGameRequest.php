<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
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
        //the Game required fields: created_user_id, type, board_id
        return [
            'created_user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:S,M',
            'board_id' => 'required|integer|exists:users,id',
        ];
    }
}
