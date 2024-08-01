<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GradeDeleteMemberRequest extends FormRequest
{
    protected $grade;

    public function authorize(): bool
    {
        $user = $this->user();
        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return false;
        }

        $grade = $this->getGrade();
        return $grade->teacher_id == $user->id;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getGrade()
    {
        if (!$this->grade) {
            $this->grade = Grade::findOrFail($this->route('gradeId'));
        }
        return $this->grade;
    }

    public function validateMember()
    {
        $memberId = $this->route('memberId');
        $grade = $this->getGrade();

        $member = $grade->members()->find($memberId);
        if (!$member) {
            throw ValidationException::withMessages([
                'member' => 'The specified member is not in this grade.',
            ]);
        }
    }

    protected function prepareForValidation()
    {
        $this->validateMember();
    }

    protected function failedAuthorization()
    {
        throw ValidationException::withMessages([
            'auth' => 'You are not authorized to delete members from this grade.',
        ]);
    }
}
