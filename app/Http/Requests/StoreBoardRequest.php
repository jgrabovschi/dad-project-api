<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoardRequest extends FormRequest
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
        // restrict the board size to a maximum of 8x8 and a minimum of 1x1 for now
        return [
            'board_cols' => 'required|integer|min:1|max:8',
            'board_rows' => 'required|integer|min:1|max:8',
        ];
    }
}
