<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskUpdateRequest extends FormRequest
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
        
        if ($this->grade->isactive == 0) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Cannot update task. The associated grade is not active.',
            ], 403));
        }
        
        $this->task = Task::where('id', $taskId)->where('grade_id', $gradeId)->first();

        if (!$this->task) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Task not found.',
            ], 404));
        }

        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can delete tasks.',
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
            'title' => 'required|string',
            'end_date' => 'required|date|after_or_equal:start_date',
            'desc' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:task_media,id',
            'links' => 'nullable|array',
            'links.*' => 'url',
            'delete_links' => 'nullable|array',
            'delete_links.*' => 'exists:task_links,id',
        ];
    }

    public function messages()
    {
        return [
            'authorize' => 'You are not authorized to perform this action for the specified grade.',
        ];
    }

    public function getTask()
    {
        return $this->task;
    }
}
