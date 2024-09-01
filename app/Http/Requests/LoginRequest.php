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
            'username' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $user = User::where('username', $value)->first();
                    if (!$user) {
                        $fail('The user is not registered.');
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $user = User::where('username', $this->username)->first();
                    if ($user && !Hash::check($value, $user->password)) {
                        $fail('The password is incorrect.');
                    }
                },
            ],
            'fcm_token'
        ];
    }


}
