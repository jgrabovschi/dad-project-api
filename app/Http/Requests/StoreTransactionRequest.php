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
            //the transaction required fields: user_id, game_id, type, brain_coins
            //the transaction optional fields: euros, payment_type, payment_reference

            // Required fields
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:B,P,I',
            'brain_coins' => 'required|integer',
            

            // Conditionally required fields
            'game_id' => 'required_if:type,I|integer|exists:games,id',
            'euros' => 'required_if:type,P|nullable|numeric|min:0',
            'payment_type' => 'required_if:type,P|nullable|string|in:MBWAY,IBAN,MB,VISA',
            'payment_reference' => 'required_if:type,P|nullable|string|max:255',
        ];
    }
}
