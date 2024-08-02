<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class TaskShowByGradeRequest extends FormRequest
{
    protected $grade;

    protected $task;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $this->grade = Grade::findOrFail($this->route('gradeId'));
        $isTeacher = $this->grade->teacher_id == $user->id;
        $isMember = $this->grade->members()->where('users.id', $user->id)->exists();
        return $isTeacher || $isMember;
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

    public function failedAuthorization()
    {
        if (!$this->grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if (!$this->grade->members->contains($this->user()->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not a member of this grade.',
            ], 403);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to view tasks for this grade.',
        ], 403);
    }

    public function getTask()
    {
        if (!$this->task) {
            $this->task = $this->grade->tasks()->with('media', 'links')->latest()->get();
        }
        return $this->task;
    }
}
