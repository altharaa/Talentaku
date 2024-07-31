<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
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
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)->first();
                    if (!$user) {
                        $fail('The email is not registered.');
                    }
                },
            ],
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $this->email)->first();
                    if ($user && !Hash::check($value, $user->password)) {
                        $fail('The password is incorrect.');
                    }
                },
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Auth::attempt($this->only('email', 'password'))) {
                $validator->errors()->add('auth', 'The provided credentials are incorrect.');
            }
        });
    }
}
