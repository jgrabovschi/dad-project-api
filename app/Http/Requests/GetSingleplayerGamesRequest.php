<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class GetSingleplayerGamesRequest extends FormRequest
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
            'board' => 'required|integer|exists:games,board_id',
            'performance' => 'required|string|in:turns,total_time',
        ];
    }
}