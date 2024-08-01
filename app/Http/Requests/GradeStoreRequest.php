<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\GradeLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GradeStoreRequest extends FormRequest
{
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $roles = $user->roles()->pluck('name')->toArray();
        $level = GradeLevel::findOrFail($this->level_id);

        $authorizedRoles = [
            'SD' => ['Guru SD'],
            'KB' => ['Guru KB'],
        ];

        if (!isset($authorizedRoles[$level->name]) || !array_intersect($roles, $authorizedRoles[$level->name])) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create a class for this level.',
            ], 403));
        }
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
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'level_id' => 'required|exists:grade_levels,id',
        ];
    }
}
