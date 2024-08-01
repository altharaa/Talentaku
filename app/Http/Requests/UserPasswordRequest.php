<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'new_password' => [
                'required',
                'min:8',
                'different:current_password',
                function ($attribute, $value, $fail) {
                    if (Hash::check($value, $this->user()->password)) {
                        $fail('The new password must be different from the current password.');
                    }
                },
            ],
            'new_password_confirmation' => 'required|same:new_password',
        ];
    }
}
