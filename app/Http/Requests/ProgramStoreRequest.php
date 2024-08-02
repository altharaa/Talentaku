<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProgramStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
