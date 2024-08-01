<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GradeJoinRequest extends FormRequest
{
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $roles = $user->roles()->pluck('name')->toArray();

        return (in_array('Murid SD', $roles) || in_array('Murid KB', $roles));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'unique_code' => 'required|string|size:5',
        ];
    }

    public function getGrade()
    {
        if (!$this->grade) {
            $this->grade = Grade::with('teacher', 'members')->where('unique_code', $this->unique_code)->first();

            if (!$this->grade) {
                throw ValidationException::withMessages([
                    'unique_code' => 'Invalid unique code. Class not found.',
                ]);
            }

            if ($this->grade->members->contains($this->user()->id)) {
                throw ValidationException::withMessages([
                    'unique_code' => 'You are already a member of this class.',
                ]);
            }
        }
        return $this->grade;
    }
}
