<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $roles = $user->roles()->pluck('name')->toArray();

        return (in_array('Guru SD', $roles) || in_array('Guru KB', $roles));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'photo' => 'nullable|image|max:2048',
            'category_id' => 'sometimes|required|exists:categories,id',
        ];
    }
}
