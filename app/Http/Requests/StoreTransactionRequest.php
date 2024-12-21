<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:B,P,I',
            'brain_coins' => 'required|integer',
            'game_id' => 'integer|exists:games,id',
            'euros' => 'required_if:type,P|nullable|numeric|min:0',
            'payment_type' => 'required_if:type,P|nullable|string|in:MBWAY,IBAN,MB,VISA,PAYPAL',
            'payment_reference' => 'required_if:type,P|nullable|string|max:255',
            'custom' => 'nullable|array'
        ];
    }
}
