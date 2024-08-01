<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskDestroyRequest extends FormRequest
{
    protected $grade;
    protected $task;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');
        $taskId = $this->route('taskId');

        try {
            $this->grade = Grade::findOrFail($gradeId);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404));
        }

        $this->task = Task::where('id', $taskId)->where('grade_id', $gradeId)->first();
        if (!$this->task) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Task not found for the specified grade.',
            ], 404));
        }

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can update tasks.',
            ], 403));
        }

        if ($user->id != $this->grade->teacher_id) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
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
           // 
        ];
    }

    public function getTask()
    {
        return $this->task;
    }
}
