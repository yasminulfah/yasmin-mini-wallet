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
            'email' => [
                'required',
                'email',
                'exists:users,email',
                function ($attribute, $value, $fail) {
                    if ($value === auth()->user()->email) {
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
            'email.required' => 'Recipient email is required.',
            'email.exists'   => 'Recipient user not found.',
            'amount.required' => 'Nominal cannot be empty.',
            'amount.integer'  => 'Nominal must be a number.',
            'amount.min'      => 'Transfer amount must be at least 1.',
        ];
    }
}
