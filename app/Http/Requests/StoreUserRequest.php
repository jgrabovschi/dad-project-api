<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            //the user required fields: name, email, password, unique nickname
            //the user optional fields: photo_filename
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nickname' => 'required|string|max:255|unique:users',
            //'photo_filename' => 'nullable|image|max:1024', // Validate as a required image file with a max size of 1MB
            'photo_filename' => 'nullable|string',
        ];
    }
}
