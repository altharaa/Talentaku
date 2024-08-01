<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GradeDisplayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // We'll handle authorization in the getGrades and getGrade methods
    }

    public function rules(): array
    {
        return [
            // Add any validation rules if needed
        ];
    }

    public function getGrades($role)
    {
        $user = $this->user();

        if ($role == 'teacher') {
            $grades = Grade::where('teacher_id', $user->id)->with('teacher', 'members')->get();
        } elseif ($role == 'member') {
            $grades = $user->grades()->with(['teacher:id,name'])->get();
        } else {
            throw ValidationException::withMessages(['role' => 'Invalid role specified']);
        }

        if ($grades->isEmpty()) {
            throw ValidationException::withMessages(['grades' => 'User has no associated grades']);
        }

        return $grades;
    }

    public function getGrade($gradeId)
    {
        $user = $this->user();
        $grade = Grade::with(['teacher', 'members'])->findOrFail($gradeId);

        $isTeacher = $grade->teacher_id == $user->id;
        $isMember = $grade->members->contains('id', $user->id);

        if (!$isTeacher && !$isMember) {
            throw ValidationException::withMessages(['permission' => 'You do not have permission to view this grade.']);
        }

        return $grade;
    }
}
