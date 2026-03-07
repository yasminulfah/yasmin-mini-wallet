<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'receiver_username' => [
                'required',
                'string',
                'exists:users,username',
                function ($attribute, $value, $fail) {
                    if ($value === auth()->user()->username) {
                        $fail('You cannot transfer money to yourself.');
                    }
                },
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($value > auth()->user()->balance) {
                        $fail('Insufficient balance.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_username.required' => 'Recipient username is required.',
            'receiver_username.exists'   => 'Recipient user not found.',
            'amount.required' => 'Nominal cannot be empty.',
            'amount.integer'  => 'Nominal must be a number.',
            'amount.min'      => 'Transfer amount must be at least 1.',
        ];
    }
}
