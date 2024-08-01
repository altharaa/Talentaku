<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\GradeLevel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GradeUpdateRequest extends FormRequest
{
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');

        try {
            $this->grade = Grade::findOrFail($gradeId);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404));
        }

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

        if ($this->grade->isactive == 0) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Cannot update grade. The associated grade is not active.',
            ], 403));
        }

        return (in_array('Guru SD', $roles) || in_array('Guru KB', $roles))
            && $user->id == $this->grade->teacher_id;
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

    public function getGrade()
    {
        if(!$this->grade)
        {
            Grade::findOrFail($this->route('gradeId'));
        }
        return $this->grade;
    }
}
