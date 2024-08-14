<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use App\Models\Grade;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class GradeContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $grade = Grade::findOrFail($this->route('gradeId'));

        if (($user->id == $grade->teacher_id) || ($grade->members->contains($user->id))) {
            return true;
        }

        throw new \HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to perform this action.',
        ], 403));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getTasks()
    {
        return Task::where('grade_id', $this->route('gradeId'))->get();
    }

    public function getAnnouncements()
    {
        return Announcement::where('grade_id', $this->route('gradeId'))
            ->with(['user', 'media', 'reply'])
            ->get();
    }

    public function getGrade()
    {
        return Grade::findOrFail($this->route('gradeId'))->only(['id', 'name']);
    }
}
